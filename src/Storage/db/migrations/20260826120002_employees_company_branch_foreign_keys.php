<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class EmployeesCompanyBranchForeignKeys extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees');

        $table->addForeignKey('company_id', 'companies', 'id', [
                'constraint' => 'fk_employees_company_id',
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
            ])
            ->addForeignKey('branch_id', 'branches', 'id', [
                'constraint' => 'fk_employees_branch_id',
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
            ])
            ->update();
    }
}
