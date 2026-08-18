<?php

namespace App\Authorization;

use App\Authorization\Authorization;
use App\Models\Permissions;
use Exception;

class RBACAuthorization implements Authorization
{
    private $users;
    private 
    public function can(
        int $userId,
        string $permission
    ): bool {
        $permissions = $this->resolvePermissions($userId);

        return isset($permissions[$permission]);
    }

    public function assertCan(
        int $userId,
        string $permission
    ): void {
        if (!$this->can($userId, $permission)) {
            throw new Exception("Forbidden ", 401);
        }
    }

    public function resolvePermissions($user): array
    {
        
    }
}
