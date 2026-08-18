<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserHasRole extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user_has_role', [
            'id' => false,
            'primary_key' => ['id'],
        ]);
        $table->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => true,
                'null' => false,
            ])
              ->addColumn('user_id', 'biginteger', [
                'signed' => false,
                'null' => false,
              ])
              ->addColumn('role_id', 'integer', ['null' => false])
              ->addForeignKey('user_id', 'users', 'id', [
                'constraint' => 'fk_user_id',
                'delete' => 'NO_ACTION',
                'update' => 'NO_ACTION',
              ])
              ->addForeignKey('role_id', 'roles', 'id', [
                'constraint' => 'fk_role_id',
                'delete' => 'NO_ACTION',
                'update' => 'NO_ACTION',
              ])
              ->create();
    }
}
