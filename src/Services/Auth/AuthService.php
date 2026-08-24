<?php

namespace App\Services\Auth;

use App\Core\Response;
use App\Models\Users;
use App\Core\Request;
use App\Models\RefreshTokens;
use Firebase\JWT\JWT;
use App\Models\ResetPasswordTokens;
use App\Storage\Database;
use App\Models\UserHasRole;
use App\Models\Roles;
use App\Services\Auth\RefreshToken;


// PHASE 1: AuthService still builds HTTP responses and sets cookies directly instead of returning data to the controller.
class AuthService
{
    private Users $users;
    private $pdo;
    private Database $db;
    public  $message;
    public  $data;
    private UserHasRole $userRole;
    private Roles $roles;
    private RefreshTokens $refreshTokens;
    private ResetPasswordTokens $resetPasswordTokens;
    private RefreshToken $refreshTokenService;

    public function __construct(RefreshTokens $refreshTokens, RefreshToken $refreshTokenService, Database $db, Roles $roles, Users $users,  ResetPasswordTokens $resetPasswordTokens, UserHasRole $userRole)
    {
        $this->users = $users;
        $this->db = $db;
        $this->pdo = $db->getPDO();
        $this->roles = $roles;
        $this->userRole = $userRole;
        $this->refreshTokens = $refreshTokens;
        $this->resetPasswordTokens = $resetPasswordTokens;
        $this->refreshTokenService = $refreshTokenService;
    }



    public function registerWithSession(string $username, string $phone, string $password)
    {
        try {
            if (!$username || !$phone || !$password) {
                return [null, "Please fill in all the details"];
            }
            if (strlen($username) < 3 || strlen($username) > 20) {
                return [null, 'Username must be between 3 and 20 characters'];
            }
            if (strlen($phone) != 10) {
                return [null, 'Phone number less than the expected characters'];
            }
            if (strlen($password) < 8 || strlen($password) > 128) {
                return [null, "Password must be at least 8 characters and a max of 128 characters and alphanumeric"];
            }
            if ($this->users->usernameExists($username)) {
                return [null, 'Username is already taken'];
            }
            $this->db->beginTransaction();
            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
            $user = $this->users->registerUser($username, $phone, $passwordHash);
            if (!$user) {
                $this->db->rollBack();
                return [null, 'Unable to create user'];
            }

            $userHasRole = $this->userRole->createUserRole($user['id']);
            if (!$userHasRole) {
                $this->db->rollBack();
                return [null, 'Unable to assign role'];
            }
            $role = $this->roles->findById($userHasRole);
            if (!$role) {
                $this->db->rollBack();
                return [null, 'Unable to resolve role'];
            }
            $token = $this->refreshTokenService->generateToken($user['id'], $role['name']);
            $data = [
                'user_id' => $user['id'],
                'token_hash' => $token['hashToken'],
                'expires_at' => $token['expiresAt']
            ];
            if ($this->refreshTokens->saveRefreshToken($data)) {
                $session = [
                    'accessToken' => $token['accessToken'],
                    'refreshToken' => $token['refreshToken'],
                    'username' => $user['username']
                ];
                $this->db->commit();
                return [$session, null];
            } else {
                $this->db->rollBack();
                return [null, 'Token creation failed'];
            }
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->db->rollBack();
            }
            return [null, 'Unable to create user'];
        }
    }


    public function changePassword($id, $currentPassword, $newPassword, $confirmPassword)
    {

        $user = $this->users->verifyUser($id, $currentPassword);

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
            $data = [
                "password" => $hashedPassword
            ];

            return $this->users->updateUser($data, $id);
        } else {
            return false;
        }
    }

    // PHASE 1: resetPassword is outside the Phase 1 gate but remains incomplete/broken if invoked.
    public function resetPassword($identifier)
    {
        $user = $this->users->getUser($identifier);
        $data = [];
        if ($user) {
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $rawSecret = bin2hex(random_bytes(32));
                $hashSecret = hash("sha256", $rawSecret);
                $deliveryChannel = "email";
                $lifespan = 900;
                $resetlink = 'https://localhost:8000/';
                $data = [
                    'user_id' => $user['id'],
                    'token_hash' => $hashSecret,
                    'expires_at' => time() + 18000,
                    'delivery_channel' => $deliveryChannel,
                    'failed_attempts' => 0
                ];
                $resetToken = $this->resetPasswordTokens->createResetToken($data);
                if ($resetToken) {
                    //notification of the user via email that is sending of email here 
                    return True;
                }
            } else {

                $rawSecret = (string) random_int(100000, 999999);
                $hashSecret = password_hash($rawSecret, PASSWORD_ARGON2ID);
                $deliveryChannel = 'phone';
                $lifespan = 300;
                $smsMessage = "Your reset 6 digit number is this" . $rawSecret;
                $data = [
                    'user_id' => $user['id'],
                    'token_hash' => $hashSecret,
                    'expires_at' => time() + $lifespan,
                    'delivery_channel' => $deliveryChannel,
                    'failed_attempts' => 0
                ];
                $resetToken = $this->resetPasswordTokens->createResetToken($data);
                if ($resetToken) {
                    // notification of user via sms
                    return True;
                }
            }
        } else {
            return false;
        }
        // PHASE 1: createResetToken() calls $stmt->pdo->execute(), which is invalid PDO usage.
        $userSave = $this->resetPasswordTokens->createResetToken($data);
        if ($userSave) {
            return true;
        }
    }
}
