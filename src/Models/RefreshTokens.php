<?php

namespace App\Models;

use App\Models\Models;

class RefreshTokens extends Models
{
    public function __construct()
    {
    }

    //CRUD
    public function saveRefreshToken($userId, $refreshToken, $expiresAt)
    {
        $query = "INSERT INTO refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $refreshToken, $expiresAt]);
        return $stmt->rowCount();
    }

    public function getRefreshToken($token)
    {
        $query = "SELECT * FROM refresh_tokens WHERE token_hash = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function updateRefreshToken($userId, $expiresAt)
    {
        //$query = "SELECT user_";
        $query = "UPDATE refresh_tokens SET expires_at = ? WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$expiresAt, $userId]);
        return $stmt->rowCount();
    }
    public function deleteRefreshToken($userId)
    {
        $query = "DELETE FROM refresh_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->rowCount();
    }
}
