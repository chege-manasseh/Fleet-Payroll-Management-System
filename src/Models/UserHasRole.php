<?php

namespace App\Models;

use PDO;
use App\Models\Roles;

class UserHasRole extends Models
{
    public function getRoleIdsForUser(int $userId): array
    {
        $query = 'SELECT role_id FROM user_has_role WHERE user_id = :user_id';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function createUserRole(int $userId, int $roleId = null)
    {
        if (!isset($roleId)) {
            $query = 'SELECT id FROM roles WHERE name=:name';
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['name' => 'employee']);
            $roleId = (int) $stmt->fetchColumn();
        }
        $query = 'INSERT INTO user_has_role (user_id,role_id) VALUES (?,?)';
        $stmt = $this->pdo->prepare($query);
        if ($stmt->execute([$userId, $roleId])) {
            return $roleId;
        }
    }
}
