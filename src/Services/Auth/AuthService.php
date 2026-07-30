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
        $this->users = new Users();
        $this->userTokens = new UserTokens();
        $this->db = $db;
        $this->response = $response;
    }

    public function register(Request $request)
    {
        try {
            $this->db->beginTransaction();
            $username = $request->input('username');
            $phone = $request->input('phone');
            $password = $request->input('password');
            echo $username . ' ' . $phone . ' ' . $password;
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
            if (strlen($password) < 8) {
                return $this->response->json('Password must be at least 8 characters', null, 400);
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

            $user = $this->users->registerUser($username, $phone, $password);
            if ($user) {
                $accessPayload = [
                    'iss' => 'fleet-payroll-management-system',
                    'iat' => time(),
                    'exp' => time() + $_ENV['JWT_EXPIRATION'],
                    'userId' => $user['id'],
                    'role' => $user['role'],
                ];
                $message = $this->message = 'User registered successfully';
                $data = $this->data = [
                    'username' => $user['username'],
                    'email' => $user['email'] ?? null,
                    'id' => $user['id'],
                    'token' => JWT::encode($accessPayload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']),
                ];
                $refreshToken = bin2hex(random_bytes(40));
                $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
                $tokenHash = hash('sha256', $refreshToken);
                $this->userTokens->saveRefreshToken($user['id'], $tokenHash, $expiresAt);
                setcookie('refresh_token', $refreshToken, [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/api/auth/refresh',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
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
        //return user object as json with username,email and id

        if ($user) {
            $accessPayload = [
                'iss' => 'fleet-payroll-management-system',
                'iat' => time(),
                'exp' => time() + $_ENV['JWT_EXPIRATION'],
                'userId' => $user['id'],
                'role' => $user['role'],
            ];
            $message = $this->message = 'Login successful';
            $data = $this->data = [
                'username' => $user['username'],
                'email' => $user['email'] ?? null,
                'id' => $user['id'],
                'token' => JWT::encode($accessPayload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']),
            ];
            $refreshToken = bin2hex(random_bytes(40));
            $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

            // 3. Save the hash of the refresh token in your database
            $tokenHash = hash('sha256', $refreshToken);
            $this->userTokens->saveRefreshToken($user['id'], $tokenHash, $expiresAt);

            //refresh token cookie
            setcookie('refresh_token', $refreshToken, [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/api/auth/refresh',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            return $this->response->json($message, $data);
        }
    }
    public function verifyEmail(Request $request)
    {
        return $this->response->json('Verify email endpoint', null, 200);
    }

    public function logout(Request $request)
    {
        return $this->response->json('Logout endpoint', null, 200);
    }

    public function changePassword(Request $request)
    {
        return $this->response->json('Change password endpoint', null, 200);
    }
}
