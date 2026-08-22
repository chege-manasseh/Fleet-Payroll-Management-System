<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Drivers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('drivers', [
            'id' => false,
            'primary_key' => ['employee_id'],
        ]);

        $table->addColumn('employee_id', 'biginteger', [
                'signed' => false,
                'null' => false,
            ])
            ->addColumn('license_number', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('license_expiry', 'date', ['null' => false])
            ->addColumn('license_class', 'string', ['limit' => 16, 'null' => false])
            ->addColumn('created_at', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('updated_at', 'timestamp', [
                'null' => true,
                'default' => null,
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['license_number'], ['unique' => true, 'name' => 'uq_drivers_license_number'])
            ->addForeignKey('employee_id', 'employees', 'id', [
                'constraint' => 'fk_drivers_employee_id',
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}
