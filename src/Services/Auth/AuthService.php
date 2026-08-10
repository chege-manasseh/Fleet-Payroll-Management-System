<?php

namespace App\Services\Auth;

use App\Core\Response;
use App\Models\Users;
use App\Core\Request;
use App\Models\RefreshTokens;
use Firebase\JWT\JWT;
use App\Models\ResetPasswordTokens;
use App\Storage\Database;

class AuthService
{
    private Users $users;
    private RefreshTokens $refreshTokens;
    public  $message;
    public  $data;
    private Database $db;
    private Response $response;
    private ResetPasswordTokens $resetPasswordTokens;

    public function __construct(Database $db, Users $users, RefreshTokens $refreshTokens, ResetPasswordTokens $resetPasswordTokens, Response $response)
    {
        $this->users = $users;
        $this->refreshTokens = $refreshTokens;
        $this->db = $db;
        $this->response = $response;
    }

    public function generateToken($userId, $role)
    {
        //symmetric cryptography 
        $accessPayload = [
            'iss' => 'fleet-payroll-management-system',
            'iat' => time(),
            'exp' => time() + $_ENV['JWT_EXPIRATION'],
            'userId' => $userId,
            'role' => $role,
        ];
        $accessToken = JWT::encode($accessPayload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);
        setcookie('access_token', $accessToken, [
            'expires' => time() + $_ENV['JWT_EXPIRATION'],
            'path' => '/api',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        $refreshToken = bin2hex(random_bytes(40));
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 
        setcookie('refresh_token', $refreshToken, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/api/refresh',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        $tokenHash = hash('sha256', $refreshToken);

        //use CSRF if the application is not Single Domain and read it and return it as a http header  that is double submit method
        // $csrfToken = bin2hex(random_bytes(32));
        // setcookie('csrf',$csrfToken,[
        //     'expires' => time() + $_ENV['JWT_EXPIRATION'],
        //     'path' => '/api',
        //     'secure' => false,
        //     'httponly' => false,
        //     'samesite' => 'None'
        // ]);

        return [
            'accessToken' => $accessToken,
            'refreshToken' => $tokenHash,
            'expiresAt' => $expiresAt,
        ];
    }

    public function register(Request $request)
    {
        try {
            $this->db->beginTransaction();
            $username = $request->input('username');
            $phone = $request->input('phone');
            $password = $request->input('password');
            $role = $request->input('role');
            //build a validater for username, phone and password

            if (!$username || !$phone || !$password) {
                return $this->response->json('Please fill in all fields {$username, $phone, $password}', null, 400);
            }
            if (strlen($username) < 3 || strlen($username) > 20) {
                return $this->response->json('Username must be between 3 and 20 characters', null, 400);
            }
            if (strlen($phone) != 10) {
                return $this->response->json('Phone number must be 10 digits', null, 400);
            }
            if (strlen($password) < 8 || strlen($password) > 128) {
                return $this->response->json('Password must be at least 8 characters and a max of 128 characters', null, 400);
            }
            if (!preg_match('/[A-Z]/', $password)) {
                return $this->response->json('Password must contain at least one uppercase letter', null, 400);
            }
            if (!preg_match('/[a-z]/', $password)) {
                return $this->response->json('Password must contain at least one lowercase letter', null, 400);
            }
            if (!preg_match('/[0-9]/', $password)) {
                return $this->response->json('Password must contain at least one number', null, 400);
            }
            if (!preg_match('/[!@#$%^&*_]/', $password)) {
                return $this->response->json('Password must contain at least one special character', null, 400);
            }

            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
            $user = $this->users->registerUser($username, $phone, $passwordHash, $role);
            if ($user) {
                $token = $this->generateToken($user['id'], 'user');
                $message = $this->message = 'User registered successfully';
                $data = $this->data = [
                    'username' => $user['username'],
                    'email' => $user['email'] ?? null,
                    'id' => $user['id']
                ];

                $this->refreshTokens->saveRefreshToken($user['id'], $token['refreshToken'], $token['expiresAt']);

                $this->db->commit();
                return $this->response->json($message, $data);
            }
        } catch (\Exception $e) {
            $this->db->rollBack();
            return $this->response->json('User registration failed', null, 401);
        }
    }


    public function login(Request $request)
    {
        // validate inputs to prevent Long password DoS
        if (!$request->input('username') || !$request->input('password')) {
            $message = $this->message = 'Please fill in all fields';
            $data = $this->data = null;
            return $this->response->json($message, $data, 400);
        }
        //build a validater for email and username and phone number

        $identifier = $request->input('username');
        $password = $request->input('password');

        $user = $this->users->verifyUser($identifier, $password);

        if ($user) {
            $token = $this->generateToken($user['id'], 'user');
            $message = $this->message = 'Login successful';
            $data = $this->data = [
                'username' => $user['username'],
                'email' => $user['email'] ?? null,
                'id' => $user['id']
            ];
            $this->refreshTokens->saveRefreshToken($user['id'], $token['refreshToken'], $token['expiresAt']);
            return $this->response->json($message, $data);
        }
    }
    public function verifyEmail(Request $request)
    {
        return $this->response->json('Verify email endpoint', null, 200);
    }

    public function logout(Request $request)
    {
        $rawToken = $request->getCookie('refresh_token');
        $hashToken = hash('sha256', $rawToken);
        if ($hashToken) {
            $expiresAt = date('Y-m-d H:i:s', time() - 3600);
            $userId = $request->input('userId');
            $data=[
                'expires_at' => $expiresAt,
                'is_revoked' => true,
                'revoked_at' => $expiresAt,
            ];
            $this->refreshTokens->updateRefreshToken($data,$hashToken);
            setcookie('refresh_token', '', time() - 3600, '/');
            setcookie('access_token', '', time() - 3600, '/');
            return $this->response->json('Logout successful', null, 200);
        }
        return $this->response->json('Logout failed.', null, 400);
    }

    public function changePassword(Request $request)
    {
        return $this->response->json('Change password endpoint', null, 200);
    }

    public function resetPassword(Request $request)
    {
        $email = $request->input("email");
        $phone = $request->input("phone");
        if (!isset($email) || !isset($phone)) {
            $message = $this->message = "Input your email or phone number";
            $data = null;
            return $this->response->json($message, $data, 401);
        }
        if (isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $this->users->getUser($email);
            if ($user) {
                $rawSecret = bin2hex(random_bytes(32));
                $deliveryChannel = 'email';
                $lifespan = 900;
                $resetlink = 'https://localhost:8000/';
                //notification of the user via email that is sending of email here 
            } else {
                return $this->response->json('No user found', null, 404);
            }
        } else {
            $user = $this->users->getUser($phone);
            if ($user) {
                $rawSecret = (string) random_int(100000, 999999);
                $deliveryChannel = 'phone';
                $lifespan = 300;
                $smsMessage = "Your reset 6 digit number is this" . $rawSecret;
                // notification of user via sms 
            } else {
                return $this->response->json('No user found', null, 404);
            }
        }
        $tokenHash = hash('sha256', $rawSecret);
        $expirationTime = date('Y-m-d H:i:s', time() + $lifespan);
        $userSave = $this->resetPasswordTokens->createResetToken();
        if ($userSave) {
            return $this->response->json('Password reset successful log in', null, 200);
        }
    }
}
