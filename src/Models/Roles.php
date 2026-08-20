<?php

namespace App\Models;

use PDO;

class Roles extends Models
{
    public function findById(int $id): ?array
    {
        $query = 'SELECT id, name, parent_role_id FROM roles WHERE id = :id LIMIT 1';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    /**
     * Expands assigned role IDs to include all ancestor roles via parent_role_id.
     *
     * @param int[] $roleIds
     * @return int[]
     */
    public function expandWithAncestors(array $roleIds): array
    {
        $all = [];

        foreach ($roleIds as $roleId) {
            $current = (int) $roleId;

            while ($current > 0) {
                if (isset($all[$current])) {
                    break;
                }

                $all[$current] = true;
                $role = $this->findById($current);

                if (!$role || empty($role['parent_role_id'])) {
                    break;
                }

                $current = (int) $role['parent_role_id'];
            }
        }

        return array_keys($all);
    }
}
