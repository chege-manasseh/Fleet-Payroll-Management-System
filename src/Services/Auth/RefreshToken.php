<?php

namespace App\Services\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Users;
use App\Models\RefreshTokens;
use App\Storage\Database;
use Dotenv\Exception\ExceptionInterface;
use Firebase\JWT\JWT;
use Exception;

// PHASE 1: Class lives under Services\Auth but is named RefreshToken (singular), which conflicts with the RefreshTokens model and PSR-4 clarity expectations.
class RefreshToken
{
    private Response $response;
    private RefreshTokens $refreshTokens;
    private Database $db;
    private AuthService $authService;
    private Users $users;


    public function __construct(Users $users, Database $db, Response $response, RefreshTokens $refreshTokens, AuthService $authService)
    {
        $this->response = $response;
        $this->refreshTokens = $refreshTokens;
        $this->db = $db;
        $this->authService = $authService;
        $this->users = $users;
    }

    public function refreshToken($hashToken)
    {
        try {
            $tokenRecord = $this->refreshTokens->getRefreshToken($hashToken);

            if ($tokenRecord === false) {
                return false;
            }

            $currentTime = time();
            if ($tokenRecord['is_revoked'] == false && strtotime($tokenRecord['expires_at']) > $currentTime) {
                $oldToken = [
                    'is_revoked' => true,
                    'revoked_at' => date('Y-m-d H:i:s', $currentTime)
                ];
                $this->refreshTokens->updateRefreshToken($oldToken, $hashToken);
                $user = $this->users->getUser($tokenRecord['user_id']);
                $tokens = $this->generateToken($user['id'], $user['role']);
                $newToken = [
                    'user_id' => $tokenRecord['user_id'],
                    'token_hash' => $tokens["hashToken"],
                    'expires_at' => $tokens['expiresAt'],
                    'parent_token_hash' => $hashToken,
                    'root_token_hash' => $tokenRecord['root_token_hash'] ?? $hashToken
                ];
                $this->refreshTokens->saveRefreshToken($newToken);
                return $tokens;
            } else {
                //invalidate all refreshtokens for this user
                if ($tokenRecord['is_revoked'] == true) {
                    // Invalidate all tokens for this user family tree
                    $this->refreshTokens->deleteRefreshToken($tokenRecord['user_id']);
                }
                return false;
            }
        } catch (Exception $e) {
            return throw new Exception("Server Error", 500);
        }
    }

    public function generateToken(int $userId, string $role)
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

        $refreshToken = bin2hex(random_bytes(40));
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 

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
            'hashToken' => $tokenHash,
            'refreshToken' => $refreshToken,
            'expiresAt' => $expiresAt,
        ];
    }
}
