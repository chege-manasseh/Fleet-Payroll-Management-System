<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Companies extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('companies', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
                'null' => false,
            ])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
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
            ->addIndex(['name'], ['unique' => true, 'name' => 'uq_companies_name'])
            ->create();
    }
}
