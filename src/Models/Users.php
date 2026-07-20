<?php

namespace App\Models;

use App\Core\Response;

class Users extends Models
{
    // protected $table = 'users';
    protected $primaryKey = 'id';
    protected $identifier = 'username';
    protected $password = 'password';
    public function __construct()
    {
        parent::__construct();
    }
    public function verifyUser($identifier, $password){
        $query = "SELECT * FROM users WHERE username = :identifier OR email = :identifier OR phone = :identifier AND password = :password";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'identifier' => $identifier,
            'password' => $password,
        ]);
        $user = $stmt->fetch();
        if($user){
            if(password_verify($password, $user['password'])){
                return $user;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function saveRefreshToken($userId, $refreshToken, $expiresAt)
    {
        $query = "INSERT INTO user_refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $refreshToken, $expiresAt]);
        return $stmt->rowCount();
    }

    public function getRefreshToken($userId)
    {
        $query = "SELECT * FROM user_refresh_tokens WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}