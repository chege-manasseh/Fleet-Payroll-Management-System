<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Roles extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('roles', ['signed' => true]);
        $table->addColumn('name', 'string', ['limit' => 64, 'null' => false])
              ->addColumn('parent_role_id', 'integer', ['null' => true, 'default' => null])
              ->addIndex(['name'], ['unique' => true])
              ->create();
    }
}
