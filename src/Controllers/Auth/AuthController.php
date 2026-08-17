<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\CookieHelper;
use App\Models\RefreshTokens;
use App\Models\Users;
use App\Services\Auth\AuthService;
use App\Services\Auth\RefreshToken;
use App\Storage\Database;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// PHASE 1: Controller passes through service results without normalizing HTTP responses for register/login/resetPassword.
class AuthController extends Controller
{
    private AuthService $authService;
    private RefreshToken $refreshTokenService;
    private Response $response;
    private RefreshTokens $refreshTokens;
    private Users $users;
    private CookieHelper $cookieHelper;

    public function __construct(CookieHelper $cookieHelper, Users $users, AuthService $authService, RefreshToken $refreshTokenService, Response $response, RefreshTokens $refreshTokens)
    {
        $this->authService = $authService;
        $this->refreshTokenService = $refreshTokenService;
        $this->response = $response;
        $this->refreshTokens = $refreshTokens;
        $this->users = $users;
        $this->cookieHelper = $cookieHelper;
    }

    public function register(Request $request)
    {

        $data = $request->getData();
        $password = $data['password'];
        $username = $data['username'];
        $phone = $data['phone'];
        $role = $data['role'];
        //build a validater for username, phone and password

        [$user, $errors] = $this->authService->register($username, $phone, $password, $role);
        if (!empty($errors)) {
            return $this->response->json('Registration failed', ['error' => $errors], 400);
        }
        if ($user) {
            $token = $this->refreshTokenService->generateToken($user['id'], $user['role']);
            $data = [
                'user_id' => $user['id'],
                'token_hash' => $token['hashToken'],
                'expires_at' => $token['expiresAt']
            ];
            if ($this->refreshTokens->saveRefreshToken($data)) {
                $this->cookieHelper->setCookies($token['accessToken'], $token['refreshToken']);

                return $this->response->json("User created successfully", ["username" => $user['username']]);
            } else {
                return $this->response->json("Token creation failed please log in ", null, 400);
            };
        }
    }

    public function login(Request $request)
    {
        $data = $request->getData();
        if (!$data['username'] || !$data['password']) {
            $message = 'Please fill in all fields';
            $data = null;
            return $this->response->json($message, $data, 400);
        }

        $identifier = $data['username'];
        $password = $data['password'];

        $user = $this->users->verifyUser($identifier, $password);
        if ($user) {
            $token = $this->refreshTokenService->generateToken($user['id'], $user['role']);
            $data = [
                'user_id' => $user['id'],
                'token_hash' => $token['hashToken'],
                'expires_at' => $token['expiresAt']
            ];
            $user = $this->refreshTokens->saveRefreshToken($data);
            if ($user) {
                $this->cookieHelper->setCookies($token['accessToken'], $token['refreshToken']);
                return $this->response->json("Log in successful", null);
            } else {
                return $this->response->json("Log in failed", ['error' => 'Token creation failed'], 400);
            }
        } else {
            return $this->response->json("Invalid credentials", null, 401);
        }
    }

    public function logout(Request $request)
    {

        $rawToken = $request->getCookie('refresh_token');
        if ($rawToken) {
            $hashToken = hash('sha256', $rawToken);
            $revokedAt = date('Y-m-d H:i:s', time() - 3600);
            $data = [
                'is_revoked' => true,
                'revoked_at' => $revokedAt,
            ];
            if ($this->refreshTokens->updateRefreshToken($data, $hashToken)) {
                $this->cookieHelper->clearCookies();
                return $this->response->json('Logout successful', null, 200);
            }
        }
        return $this->response->json('Logout failed.', ['error' => 'no refresh token'], 400);
    }
    public function changePassword(Request $request)
    {


        $user = $request->getAttribute('user');
        if (!$user) {
            return $this->response->json('Unauthorized', null, 401);
        }
        $userId = $user->userId;
        $data = $request->getData();
        $currentPassword = $data['current_password'];
        $newPassword = $data['new_password'];
        $confirmPassword = $data['confirm_password'];


        if (!$currentPassword || !$newPassword || !$confirmPassword) {
            return $this->response->json('All password fields are required', null, 400);
        }
        if (hash_equals($currentPassword, $newPassword) || $newPassword !== $confirmPassword) {
            return $this->response->json("Passwords do not match ", null, 400);
        }

        $user = $this->authService->changePassword($userId, $currentPassword, $newPassword, $confirmPassword);
        if ($user) {
            $this->cookieHelper->clearCookies();
            $this->refreshTokens->deleteRefreshToken($userId);
            return $this->response->json("Password updated successfully", ["id" => $userId]);
        } else {
            return $this->response->json("Password update failed", null, 401);
        }
    }

    public function resetPassword(Request $request)
    {
        $email = $request->input("email");
        $phone = $request->input("phone");

        if (!isset($email) || !isset($phone)) {
            return $this->response->json("Fill in the required fields", null, 400);
        }
        if (!isset($email) && !empty($phone)) {
            $reset =  $this->authService->resetPassword($phone);
        } else {
            $reset =  $this->authService->resetPassword($email);
        }
        if ($reset) {
            return $this->response->json("Password reset sent to your mobile or email", ["success" => True]);
        } else {
            return $this->response->json("Password reset failed try again", ["success" => False]);
        }
    }
    // public function verifyResetPassword(Request $request){
    //     return $this->authService->verifyResetPassword($request,$this->response);
    // }
    // public function verifyChangePassword(Request $request){
    //     return $this->authService->verifyChangePassword($request,$this->response);
    // }

    // PHASE 1: refresh() exists here but the router registers RefreshToken::class directly, bypassing this controller method.
    public function refresh(Request $request)
    {
        $rawToken = $request->getCookie('refresh_token');
        if (!$rawToken) {
            $message = 'Refresh Token Invalid';
            $data = null;
            return $this->response->json($message, $data, 401);
        }
        $hashToken = hash('sha256', $rawToken);

        $refresh = $this->refreshTokenService->refreshToken($hashToken);
        if ($refresh) {
            $this->cookieHelper->clearCookies();
            $this->cookieHelper->setCookies($refresh['accessToken'], $refresh['refreshToken']);
            return $this->response->json("Token Refreshed", ['success' => TRUE]);
        } else {
            return $this->response->json("Log in again kindly", ['success' => False], 400);
        }
    }
}
