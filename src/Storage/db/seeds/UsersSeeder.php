<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
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
                'username' => 'admin',
                'phone' => '0714263306',
                'password' => password_hash('admin123', PASSWORD_ARGON2ID),
            ],
            [
                'username' => 'office_staff',
                'phone' => '0711000001',
                'password' => password_hash('employee123', PASSWORD_ARGON2ID),
            ],
            [
                'username' => 'driver_james',
                'phone' => '0711000002',
                'password' => password_hash('driver123', PASSWORD_ARGON2ID),
            ],
            [
                'username' => 'fleet_mgr_sam',
                'phone' => '0711000003',
                'password' => password_hash('fleet123', PASSWORD_ARGON2ID),
            ],
            [
                'username' => 'payroll_grace',
                'phone' => '0711000004',
                'password' => password_hash('payroll123', PASSWORD_ARGON2ID),
            ],
        ];
        $users = $this->table('users');
        $users->insert($data)
            ->saveData();
    }
}
