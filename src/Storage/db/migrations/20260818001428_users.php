<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Users extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users', [
            'id' => false,
            'primary_key' => ['id'],
        ]);
        $table->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
                'null' => false,
            ])
              ->addColumn('username', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('phone', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('created_at', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
              ])
              ->addColumn('role', 'string', [
                'limit' => 100,
                'null' => true,
                'default' => 'employee',
              ])
              ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
              ->create();
    }
}
