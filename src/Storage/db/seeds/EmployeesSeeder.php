<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EmployeesSeeder extends AbstractSeed
{
    #[Override]
    public function getDependencies(): array
    {
        return [
            'UsersSeeder',
            'CompaniesSeeder',
            'BranchesSeeder',
        ];
    }

    public function run(): void
    {
        $users = $this->fetchAll('SELECT id, username, phone FROM users');
        $userIds = [];
        foreach ($users as $user) {
            $userIds[$user['username']] = [
                'id' => (int) $user['id'],
                'phone' => $user['phone'],
            ];
        }

        $company = $this->fetchRow(
            "SELECT id FROM companies WHERE name = 'East Africa Fleet Logistics Ltd' LIMIT 1"
        );
        if (!$company) {
            throw new RuntimeException('Company not found');
        }
        $companyId = (int) $company['id'];

        $branches = $this->fetchAll('SELECT id, name FROM branches WHERE company_id = ' . $companyId);
        $branchIds = [];
        foreach ($branches as $branch) {
            $branchIds[$branch['name']] = (int) $branch['id'];
        }

        $employees = [
            'admin' => [
                'full_name' => 'Chege Admin',
                'national_id' => '12345678',
                'employment_type' => 'full_time',
                'base_salary' => 150000.00,
                'hire_date' => '2023-01-10',
                'branch' => 'Nairobi',
            ],
            'office_staff' => [
                'full_name' => 'Jane Wanjiku',
                'national_id' => '23456789',
                'employment_type' => 'full_time',
                'base_salary' => 45000.00,
                'hire_date' => '2024-03-15',
                'branch' => 'Nairobi',
            ],
            'driver_james' => [
                'full_name' => 'James Otieno',
                'national_id' => '34567890',
                'employment_type' => 'full_time',
                'base_salary' => 55000.00,
                'hire_date' => '2024-06-01',
                'branch' => 'Mombasa',
            ],
            'fleet_mgr_sam' => [
                'full_name' => 'Samuel Kiprop',
                'national_id' => '45678901',
                'employment_type' => 'full_time',
                'base_salary' => 85000.00,
                'hire_date' => '2023-08-20',
                'branch' => 'Mombasa',
            ],
            'payroll_grace' => [
                'full_name' => 'Grace Mwangi',
                'national_id' => '56789012',
                'employment_type' => 'full_time',
                'base_salary' => 75000.00,
                'hire_date' => '2024-01-05',
                'branch' => 'Kisumu',
            ],
        ];

        $employeeRows = [];

        foreach ($employees as $username => $profile) {
            if (!isset($userIds[$username])) {
                throw new RuntimeException("User '{$username}' not found");
            }
            if (!isset($branchIds[$profile['branch']])) {
                throw new RuntimeException("Branch '{$profile['branch']}' not found");
            }

            $employeeRows[] = [
                'user_id' => $userIds[$username]['id'],
                'company_id' => $companyId,
                'branch_id' => $branchIds[$profile['branch']],
                'full_name' => $profile['full_name'],
                'phone' => $userIds[$username]['phone'],
                'national_id' => $profile['national_id'],
                'employment_type' => $profile['employment_type'],
                'base_salary' => $profile['base_salary'],
                'hire_date' => $profile['hire_date'],
                'status' => 'active',
            ];
        }

        $this->table('employees')
            ->insert($employeeRows)
            ->saveData();
    }
}
