<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PermissionsSeeder extends AbstractSeed
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
            // Profile (everyone)
            ['name' => 'profile.view'],
            ['name' => 'profile.update'],

            // Users & access control
            ['name' => 'users.view'],
            ['name' => 'users.create'],
            ['name' => 'users.update'],
            ['name' => 'users.delete'],

            ['name' => 'roles.view'],
            ['name' => 'roles.create'],
            ['name' => 'roles.update'],
            ['name' => 'roles.delete'],

            ['name' => 'permissions.view'],
            ['name' => 'permissions.create'],
            ['name' => 'permissions.update'],
            ['name' => 'permissions.delete'],

            // Organization (Phase 2)
            ['name' => 'companies.view'],
            ['name' => 'companies.create'],
            ['name' => 'companies.update'],
            ['name' => 'companies.delete'],

            ['name' => 'branches.view'],
            ['name' => 'branches.create'],
            ['name' => 'branches.update'],
            ['name' => 'branches.delete'],

            // Workforce (Phase 3)
            ['name' => 'employees.view'],
            ['name' => 'employees.create'],
            ['name' => 'employees.update'],
            ['name' => 'employees.delete'],

            ['name' => 'drivers.view'],
            ['name' => 'drivers.create'],
            ['name' => 'drivers.update'],
            ['name' => 'drivers.delete'],

            // Fleet (Phase 4)
            ['name' => 'fleet.view'],
            ['name' => 'fleet.create'],
            ['name' => 'fleet.update'],
            ['name' => 'fleet.delete'],
            ['name' => 'fleet.assign'],

            // Trips (Phase 5)
            ['name' => 'trips.view'],
            ['name' => 'trips.view_own'],
            ['name' => 'trips.create'],
            ['name' => 'trips.update'],
            ['name' => 'trips.assign'],
            ['name' => 'trips.start'],
            ['name' => 'trips.complete'],
            ['name' => 'trips.cancel'],

            // Payroll (Phase 6)
            ['name' => 'payroll.view'],
            ['name' => 'payroll.create'],
            ['name' => 'payroll.run'],
            ['name' => 'payroll.approve'],
            ['name' => 'payroll.lock'],

            // Payments (Phase 7)
            ['name' => 'payments.view'],
            ['name' => 'payments.create'],
            ['name' => 'payments.retry'],
        ];
        $permissions = $this->table('permissions');
        $permissions->insert($data)
            ->saveData();
    }
}
