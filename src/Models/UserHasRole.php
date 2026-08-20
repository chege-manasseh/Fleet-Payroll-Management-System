<?php

namespace App\Models;

use PDO;

class UserHasRole extends Models
{
    public function getRoleIdsForUser(int $userId): array
    {
        $query = 'SELECT role_id FROM user_has_role WHERE user_id = :user_id';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
