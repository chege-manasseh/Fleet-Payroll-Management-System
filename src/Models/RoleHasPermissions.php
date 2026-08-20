<?php

namespace App\Models;

use PDO;

class RoleHasPermissions extends Models
{
    /**
     * @param int[] $roleIds
     * @return string[]
     */
    public function getPermissionNamesForRoles(array $roleIds): array
    {
        if (empty($roleIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($roleIds), '?'));
        $query = "SELECT DISTINCT permissions.name
                  FROM role_has_permissions
                  INNER JOIN permissions ON permissions.id = role_has_permissions.permission_id
                  WHERE role_has_permissions.role_id IN ($placeholders)";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array_values($roleIds));

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
