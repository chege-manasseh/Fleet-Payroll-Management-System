<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Branches extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('branches', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
                'null' => false,
            ])
            ->addColumn('company_id', 'biginteger', [
                'signed' => false,
                'null' => false,
            ])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('location', 'string', ['limit' => 255, 'null' => true])
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
            ->addIndex(['company_id', 'name'], ['unique' => true, 'name' => 'uq_branches_company_name'])
            ->addForeignKey('company_id', 'companies', 'id', [
                'constraint' => 'fk_branches_company_id',
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}
