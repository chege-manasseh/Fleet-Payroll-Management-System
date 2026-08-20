<?php

namespace App\Services\Authorization;

use App\Models\Roles;
use App\Models\RoleHasPermissions;
use App\Models\UserHasRole;

class RolePermissionService
{
    private Roles $roles;
    private UserHasRole $userRole;
    private RoleHasPermissions $rolePermissions;

    public function __construct(
        UserHasRole $userRole,
        RoleHasPermissions $rolePermissions,
        Roles $roles
    ) {
        $this->userRole = $userRole;
        $this->rolePermissions = $rolePermissions;
        $this->roles = $roles;
    }

    public function resolvePermissions(int $userId): array
    {
        $directRoleIds = $this->userRole->getRoleIdsForUser($userId);

        if (empty($directRoleIds)) {
            return [];
        }

        $allRoleIds = $this->roles->expandWithAncestors($directRoleIds);
        $permissionNames = $this->rolePermissions->getPermissionNamesForRoles($allRoleIds);

        return array_fill_keys($permissionNames, true);
    }
}
