<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DriversSeeder extends AbstractSeed
{
    #[Override]
    public function getDependencies(): array
    {
        return [
            'EmployeesSeeder',
            'UserHasRoleSeeder',
        ];
    }

    public function run(): void
    {
        $driverUsers = $this->fetchAll(
            "SELECT e.id AS employee_id, u.username
             FROM employees e
             INNER JOIN users u ON u.id = e.user_id
             INNER JOIN user_has_role uhr ON uhr.user_id = u.id
             INNER JOIN roles r ON r.id = uhr.role_id
             WHERE r.name = 'driver'"
        );

        if (empty($driverUsers)) {
            throw new RuntimeException('No employees with the driver role found');
        }

        $licenseProfiles = [
            'driver_james' => [
                'license_number' => 'DL-KE-2024-001234',
                'license_expiry' => '2028-06-30',
                'license_class' => 'CE',
            ],
        ];

        $driverRows = [];

        foreach ($driverUsers as $driverUser) {
            $username = $driverUser['username'];

            if (!isset($licenseProfiles[$username])) {
                throw new RuntimeException("Missing driver license profile for '{$username}'");
            }

            $profile = $licenseProfiles[$username];

            $driverRows[] = [
                'employee_id' => (int) $driverUser['employee_id'],
                'license_number' => $profile['license_number'],
                'license_expiry' => $profile['license_expiry'],
                'license_class' => $profile['license_class'],
            ];
        }

        $this->table('drivers')
            ->insert($driverRows)
            ->saveData();
    }
}
