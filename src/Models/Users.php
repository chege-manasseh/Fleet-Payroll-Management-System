<?php

namespace App\Models;

use App\Core\Response;
use PDO;

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

    public function registerUser($username, $phone, $password, $role)
    {
        $query = "INSERT INTO users (username, phone, password,role) VALUES (?, ?, ?,?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$username, $phone, $password, $role]);
        $user = $this->pdo->lastInsertId();
        if ($user) {
            return $user;
        }
    }

    public function verifyUser($identifier, $password)
    {
        $query = "SELECT * FROM users WHERE username = :identifier OR email = :identifier OR phone = :identifier AND password = :password";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'identifier' => $identifier,
            'password' => $password,
        ]);
        $user = $stmt->fetch();
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $user = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUser($identifier)
    {
        $identifier = trim($identifier);
        $query = "SELECT FROM users WHERE email = :indentifier OR phone= :indentifier OR id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt = $this->pdo->execute([
            'email' => $identifier,
            'phone' => $identifier
        ]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userRow) {
            return [
                'id' => $userRow['id'],
                'email' => $userRow['email'],
                'phone' => $userRow['phone'],
            ];
        } else {
            return false;
        }
    }

    public function updateUser($data, $id)
    {

        $columns = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $columns = "`$column` = :$column";
            $bindings[":$column"] = $value;
        }
        $columnString = implode(",", $columns);
        $bindings[":id"] = $id;
        $sql = "UPDATE users SET $columnString WHERE `id`=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt = $this->pdo->execute([$bindings]);

        if ($stmt) {
            return true;
        }else{
            return false;
        }
    }
}
