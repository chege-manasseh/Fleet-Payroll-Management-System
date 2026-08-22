<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Employees extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
                'null' => false,
            ])
            ->addColumn('user_id', 'biginteger', [
                'signed' => false,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('company_id', 'biginteger', [
                'signed' => false,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('branch_id', 'biginteger', [
                'signed' => false,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('full_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('national_id', 'string', ['limit' => 32, 'null' => true])
            ->addColumn('employment_type', 'string', [
                'limit' => 32,
                'null' => false,
                'default' => 'full_time',
            ])
            ->addColumn('base_salary', 'decimal', [
                'precision' => 12,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('hire_date', 'date', ['null' => true])
            ->addColumn('status', 'string', [
                'limit' => 32,
                'null' => false,
                'default' => 'active',
            ])
            ->addColumn('created_at', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('updated_at', 'timestamp', [
                'null' => true,
                'default' => null,
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['user_id'], ['unique' => true, 'name' => 'uq_employees_user_id'])
            ->addIndex(['national_id'], ['unique' => true, 'name' => 'uq_employees_national_id'])
            ->addIndex(['company_id'])
            ->addIndex(['branch_id'])
            ->addIndex(['status'])
            ->addForeignKey('user_id', 'users', 'id', [
                'constraint' => 'fk_employees_user_id',
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}
