<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RoleHasPermissions extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('role_has_permissions', [
            'id' => false,
            'primary_key' => ['id'],
        ]);
        $table->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
                'null' => false,
            ])
              ->addColumn('role_id', 'integer', ['null' => false])
              ->addColumn('permission_id', 'integer', ['null' => false])
              ->addForeignKey('role_id', 'roles', 'id', [
                'constraint' => 'fk_role',
                'delete' => 'NO_ACTION',
                'update' => 'NO_ACTION',
              ])
              ->addForeignKey('permission_id', 'permissions', 'id', [
                'constraint' => 'fk_permission',
                'delete' => 'NO_ACTION',
                'update' => 'NO_ACTION',
              ])
              ->create();
    }
}
