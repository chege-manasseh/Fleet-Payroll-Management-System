<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class UserHasRoleSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */

    #[Override]
    public function getDependencies(): array
    {
        return [
            'UsersSeeder',
            'RolesSeeder'
        ];
    }
    public function run(): void
    {
        $roles = $this->fetchAll('SELECT id,name FROM roles');
        $users = $this->fetchAll('SELECT id,username FROM users');
        $usersIds = [];
        foreach ($users as $user) {
            $usersIds[$user['username']] = $user['id'];
        }
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['name']] = $role['id'];
        }
        $roleAssignments = [
            'admin'           => 'admin',
            'office_staff'    => 'employee',
            'driver_james'    => 'driver',
            'fleet_mgr_sam'   => 'fleet_manager',
            'payroll_grace'   => 'payroll_officer',
        ];

        $userHasRoleData = [];
        foreach ($roleAssignments as $username => $roleName) {
            if (!isset($usersIds[$username])) {
                throw new RuntimeException("User '{$username}' not found");
            }
            if (!isset($roleIds[$roleName])) {
                throw new RuntimeException("Role '{$roleName}' not found");
            }
            $userHasRoleData[] = [
                'user_id' => $usersIds[$username],
                'role_id' => $roleIds[$roleName],
            ];
        }
        $userHasRole = $this->table('user_has_role');
        $userHasRole->insert($userHasRoleData)
            ->saveData();
    }
}
