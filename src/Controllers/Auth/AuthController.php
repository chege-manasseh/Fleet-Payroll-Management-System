<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Response;
use App\Models\Users;
use App\Core\Request;
use Firebase\JWT\JWT;


class AuthController extends Controller
{
    private Users $users;
    public  $message;
    public  $data;
    public function __construct()
    {
        $this->users = new Users();
    }

    public function register(Request $request)
    {
        $response = new Response('Register endpoint', null, 200);
        return $response->send();
    }


    public function login(Request $request)
    {
        if (!$request->input('username') || !$request->input('password')) {
            $message = $this->message = 'Please fill in all fields';
            $data = $this->data = null;
            return $this->json($message, $data, 400);
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
                'email' => $user['email'],
                'id' => $user['id'],
                'token' => JWT::encode($accessPayload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']),
            ];
            $refreshToken = bin2hex(random_bytes(40));
            $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

            // 3. Save the hash of the refresh token in your database
            $tokenHash = hash($_ENV['JWT_ALGORITHM'], $refreshToken);
            $this->users->saveRefreshToken($user['id'], $tokenHash, $expiresAt);

            //refresh token cookie
            setcookie('refresh_token', $refreshToken, [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/api/auth/refresh',
                'secure' => true,         
                'httponly' => true,       
                'samesite' => 'Strict'    
            ]);
            return $this->json($message, $data);
        }
    }
    public function verifyEmail()
    {
        $response = new Response('Verify email endpoint', null, 200);
        return $response->send();
    }

    public function logout()
    {
        $response = new Response('Logout endpoint', null, 200);
        return $response->send();
    }

    public function changePassword()
    {
        $response = new Response('Change password endpoint', null, 200);
        return $response->send();
    }
}
