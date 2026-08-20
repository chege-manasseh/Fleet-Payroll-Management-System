<?php

namespace App\Authorization;

use App\Services\Authorization\RolePermissionService;

class RBACAuthorization implements Authorization
{
    private RolePermissionService $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    public function can(int $userId, string $permission): bool
    {
        $permissions = $this->resolvePermissions($userId);

        return isset($permissions[$permission]);
    }

    public function assertCan(int $userId, string $permission): void
    {
        if (!$this->can($userId, $permission)) {
            throw new ForbiddenException();
        }
    }

    public function resolvePermissions(int $userId): array
    {
        return $this->rolePermissionService->resolvePermissions($userId);
    }
}
