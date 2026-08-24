<?php

namespace App\Models;

use App\Core\Response;
use PDO;

class Users extends Models
{

    public function usernameExists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);

        return (bool) $stmt->fetchColumn();
    }

    public function registerUser($username, $phone, $password)
    {
        $query = "INSERT INTO users (username, phone, password) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$username, $phone, $password]);
        $user = $this->pdo->lastInsertId();
        $sql = "SELECT * FROM users WHERE id =:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return $user;
        }
    }

    public function verifyUser($identifier, $password)
    {

        $query = "SELECT u.id, u.password,u.username, r.name AS role FROM users u INNER JOIN user_has_role uhr ON u.id=uhr.user_id INNER JOIN roles r ON r.id=uhr.role_id WHERE u.username = :identifier OR u.email = :identifier OR u.phone = :identifier OR u.id =:identifier LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'identifier' => $identifier
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
        $query = "SELECT * FROM users WHERE email = :indentifier OR phone= :indentifier OR id = :indentifier LIMIT 1";
        $stmt = $this->pdo->prepare($query);

        $stmt->execute([
            'identifier' => $identifier,
        ]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userRow) {
            return [
                'id' => $userRow['id'],
                'email' => $userRow['email'],
                'phone' => $userRow['phone'],
                'role' => $userRow['role']
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

            $columns[] = "`$column` = :$column";
            $bindings[":$column"] = $value;
        }
        $columnString = implode(",", $columns);
        $bindings[":id"] = $id;
        $sql = "UPDATE users SET $columnString WHERE `id`=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        if ($stmt) {
            return true;
        } else {
            return false;
        }
    }
}
