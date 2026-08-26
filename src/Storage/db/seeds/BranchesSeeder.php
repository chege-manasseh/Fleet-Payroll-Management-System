<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class BranchesSeeder extends AbstractSeed
{
    #[Override]
    public function getDependencies(): array
    {
        return [
            'CompaniesSeeder',
        ];
    }

    public function run(): void
    {
        $company = $this->fetchRow(
            "SELECT id FROM companies WHERE name = 'East Africa Fleet Logistics Ltd' LIMIT 1"
        );

        if (!$company) {
            throw new RuntimeException('Company not found. Run CompaniesSeeder first.');
        }

        $companyId = (int) $company['id'];

        $branches = [
            [
                'company_id' => $companyId,
                'name' => 'Nairobi',
                'location' => 'Nairobi, Kenya',
                'status' => 'active',
            ],
            [
                'company_id' => $companyId,
                'name' => 'Mombasa',
                'location' => 'Mombasa, Kenya',
                'status' => 'active',
            ],
            [
                'company_id' => $companyId,
                'name' => 'Kisumu',
                'location' => 'Kisumu, Kenya',
                'status' => 'active',
            ],
            [
                'company_id' => $companyId,
                'name' => 'Kampala',
                'location' => 'Kampala, Uganda',
                'status' => 'active',
            ],
        ];

        $this->table('branches')
            ->insert($branches)
            ->saveData();
    }
}
