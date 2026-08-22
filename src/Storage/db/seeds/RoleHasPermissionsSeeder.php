<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RoleHasPermissionsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */

    public function getDependencies(): array
    {
        return [
            'RolesSeeder',
            'PermissionsSeeder'
        ];
    }
    public function run(): void
    {

        $roles = $this->fetchAll('SELECT id,name FROM roles');
        $permissions = $this->fetchAll('SELECT id,name FROM permissions');
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['name']] = $role['id'];
        }
        $permissionsIds = [];
        foreach ($permissions as $permission) {
            $permissionsIds[$permission['name']] = $permission['id'];
        }
        $rolePermissions = [
            'employee' => [
                'profile.view',
                'profile.update',
            ],

            'driver' => [
                'trips.view_own',
                'trips.start',
                'trips.complete',
            ],

            'fleet_manager' => [
                'employees.view',
                'drivers.view',
                'fleet.view',
                'fleet.create',
                'fleet.update',
                'fleet.assign',
                'trips.view',
                'trips.create',
                'trips.update',
                'trips.assign',
                'trips.cancel',
                'branches.view',
            ],

            'payroll_officer' => [
                'employees.view',
                'payroll.view',
                'payroll.create',
                'payroll.run',
                'payroll.approve',
                'payroll.lock',
                'payments.view',
                'payments.create',
                'payments.retry',
            ],

            'admin' => [
                // System / users / RBAC
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
                'permissions.view',
                'permissions.create',
                'permissions.update',
                'permissions.delete',

                // Organization
                'companies.view',
                'companies.create',
                'companies.update',
                'companies.delete',
                'branches.view',
                'branches.create',
                'branches.update',
                'branches.delete',

                // Full workforce + fleet + trips (admin override)
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.delete',
                'drivers.view',
                'drivers.create',
                'drivers.update',
                'drivers.delete',
                'fleet.view',
                'fleet.create',
                'fleet.update',
                'fleet.delete',
                'fleet.assign',
                'trips.view',
                'trips.view_own',
                'trips.create',
                'trips.update',
                'trips.assign',
                'trips.start',
                'trips.complete',
                'trips.cancel',

                // Payroll + payments (also inherited from payroll_officer, listed for clarity)
                'payroll.view',
                'payroll.create',
                'payroll.run',
                'payroll.approve',
                'payroll.lock',
                'payments.view',
                'payments.create',
                'payments.retry',
            ],
        ];

        $roleHasPermissionsData = [];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                $roleHasPermissionsData[] = [
                    'role_id' => $roleIds[$roleName],
                    'permission_id' => $permissionsIds[$permissionName],
                ];
            }
        }
        $roleHasPermissions = $this->table('role_has_permissions');
        $roleHasPermissions->insert($roleHasPermissionsData)
            ->saveData();
    }
}
