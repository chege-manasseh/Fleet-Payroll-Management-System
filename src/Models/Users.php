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

    public function registerUser($username, $phone, $password){
        $password = password_hash($password,PASSWORD_BCRYPT);
        $query = "INSERT INTO users (username, phone, password) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$username, $phone, $password]);
        $userId = $this->pdo->lastInsertId();
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if($user){
            return $user;
        }
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

}