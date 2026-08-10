<?php

namespace App\Services\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Users;
use App\Models\RefreshTokens;
use App\Storage\Database;
use Dotenv\Exception\ExceptionInterface;
use Exception;

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

    public function refreshToken(Request $request)
    {
        $this->db->beginTransaction();
        $rawToken = $request->getCookie('refresh_token');
        if (!$rawToken) {
            $message = 'Refresh Token Invalid';
            $data = null;
            return $this->response->json($message, $data, 401);
        }
        $hashToken = hash('sha256', $rawToken);
        try {
            $tokenRecord = $this->refreshTokens->getRefreshToken($hashToken);

            if ($tokenRecord === false) {
                $this->db->rollBack();
                return $this->response->json('Unauthorized', null, 401);
            }

            $currentTime = time();
            if ($tokenRecord['is_revoked'] == false || strtotime($tokenRecord['expires_at']) > $currentTime) {
                $oldToken = [
                    'is_revoked' => true,
                    'revoked_at' => date('Y-m-d H:i:s', $currentTime)
                ];
                $this->refreshTokens->updateRefreshToken($oldToken, $hashToken);
                $user = $this->users->getUser($tokenRecord['user_id']);
                $tokens = $this->authService->generateToken($tokenRecord['user_id'], $user['role']);
                $newToken = [
                    'user_id' => $tokenRecord['user_id'],
                    'token_hash' => $tokens["refreshToken"],
                    'expires_at' => $tokens['expiresAt'],
                    'parent_token_hash' => $hashToken,
                    'root_token_hash' => $tokenRecord['root_token_hash'] ?? $hashToken
                ];
                $this->refreshTokens->saveRefreshToken($newToken);
                $this->db->commit();
                return $this->response->json("token refreshed", null, 200);
            } else {
                //invalidate all refreshtokens for this user
                if ($tokenRecord['is_revoked'] == true) {
                    // Invalidate all tokens for this user family tree
                    $this->refreshTokens->deleteRefreshToken($tokenRecord['user_id']);
                }
                $this->db->commit();
                $this->response->json("UNAUTHORIZED LOG IN A FRESH", null, 401);
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            return $this->response->json("Server Error", ['error' => $e->getMessage()], 500);
        }
    }
}
