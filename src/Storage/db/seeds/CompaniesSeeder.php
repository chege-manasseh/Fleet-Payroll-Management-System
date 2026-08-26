<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CompaniesSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'East Africa Fleet Logistics Ltd',
                'status' => 'active',
            ],
        ];

        $this->table('companies')
            ->insert($data)
            ->saveData();
    }
}
