<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RoleHierarchySeeder extends AbstractSeed
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
            'RolesSeeder'
        ];
    }
    public function run(): void
    {
        $roles = $this->fetchAll('SELECT id,name FROM roles');
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['name']] = $role['id'];
        }
        $hierarchy = [
            'employee'        => null,
            'driver'          => 'employee',
            'fleet_manager'   => 'driver',
            'payroll_officer' => 'employee',
            'admin'           => 'employee',
        ];
        foreach ($hierarchy as $roleName => $parentName) {
            if (!isset($roleIds[$roleName])) {
                continue;
            }
            if ($parentName === null) {
                $this->execute(
                    'UPDATE roles SET parent_role_id = NULL WHERE name = ?',
                    [$roleName]
                );
                continue;
            }
            if (!isset($roleIds[$parentName])) {
                throw new RuntimeException("Parent role '{$parentName}' not found for '{$roleName}'");
            }
            $this->execute(
                'UPDATE roles SET parent_role_id = ? WHERE name = ?',
                [$roleIds[$parentName], $roleName]
            );
        }
    }
}
