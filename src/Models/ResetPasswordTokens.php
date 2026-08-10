<?php
namespace App\Models;


class ResetPasswordTokens extends Models
{
    public function __construct() {
    }

    public function createResetToken($id,$tokenHash,$expirationTime,$deliveryChannel){
        $query = "INSERT INTO password_reset_tokens (user_id,token_hash,expires_at,delivery_channel)VALUES (? ? ? ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt = $this->pdo->execute([$id,$tokenHash,$expirationTime,$deliveryChannel]);
        $stmt = $this->pdo->fetch();
        return $stmt;
        

    }
}