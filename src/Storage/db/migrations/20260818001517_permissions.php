<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Permissions extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('permissions', ['signed' => true]);
        $table->addColumn('name', 'string', ['limit' => 64, 'null' => false])
              ->addIndex(['name'], ['unique' => true])
              ->create();
    }
}
