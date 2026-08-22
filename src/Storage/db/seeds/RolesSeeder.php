<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RolesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */

    public function run(): void
    {

        $data = [
            [
                'name' => 'employee',
                'parent_role_id' => null
            ],
            [
                'name' => 'driver',
                'parent_role_id' => null
            ],
            [
                'name' => 'fleet_manager',
                'parent_role_id' => null
            ],
            [
                'name' => 'payroll_officer',
                'parent_role_id' => null
            ],
            [
                'name' => 'admin',
                'parent_role_id' => null
            ]
        ];
        $roles = $this->table('roles');
        $roles->insert($data)
            ->saveData();
    }
}
