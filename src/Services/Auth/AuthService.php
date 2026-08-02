<?php

namespace App\Services\Auth;

use App\Core\Response;
use App\Models\Users;
use App\Core\Request;
use Firebase\JWT\JWT;
use App\Models\UserTokens;
use App\Storage\Database;

class AuthService
{
    private Users $users;
    private UserTokens $userTokens;
    public  $message;
    public  $data;
    private Database $db;
    private Response $response;

    public function __construct(Database $db, Users $users, UserTokens $userTokens, Response $response)
    {
        $this->users =$users;
        $this->userTokens = $userTokens;
        $this->db = $db;
        $this->response = $response;
    }

    public function generateToken($userId, $role)
    {
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

            $passwordHash=password_hash($password,PASSWORD_ARGON2ID);
            $user = $this->users->registerUser($username, $phone, $passwordHash);
            if ($user) {
                $token = $this->generateToken($user['id'], 'user');
                $message = $this->message = 'User registered successfully';
                $data = $this->data = [
                    'username' => $user['username'],
                    'email' => $user['email'] ?? null,
                    'id' => $user['id'],
                    'token' => $token['accessToken'],
                ];

                $this->userTokens->saveRefreshToken($user['id'], $token['refreshToken'], $token['expiresAt']);

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
                'id' => $user['id'],
                'token' => $token['accessToken'],
            ];
            $this->userTokens->saveRefreshToken($user['id'], $token['refreshToken'], $token['expiresAt']);
            return $this->response->json($message, $data);
        }
    }
    public function verifyEmail(Request $request)
    {
        return $this->response->json('Verify email endpoint', null, 200);
    }

    public function logout(Request $request)
    {
        $refreshToken = $request->getCookie('refresh_token');
        echo $refreshToken;
        if ($refreshToken) {
            $expiresAt = date(now());
            $userId = $request->input('userId');
            $this->userTokens->updateRefreshToken($userId, $expiresAt);
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
}
