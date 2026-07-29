<?php

namespace App\Models;

use App\Models\Models;

class UserTokens extends Models
{
    public function __construct()
    {
        parent::__construct();
    }

    public function saveRefreshToken($userId, $refreshToken, $expiresAt)
    {
        $query = "INSERT INTO user_verify_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $refreshToken, $expiresAt]);
        return $stmt->rowCount();
    }

    public function getRefreshToken($userId)
    {
        $query = "SELECT * FROM user_verify_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function deleteRefreshToken($userId)
    {
        $query = "DELETE FROM user_verify_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->rowCount();
    }
}
