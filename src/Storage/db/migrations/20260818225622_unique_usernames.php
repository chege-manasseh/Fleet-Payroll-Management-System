<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UniqueUsernames extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'DELETE uhr FROM user_has_role uhr
             INNER JOIN users u ON u.id = uhr.user_id
             INNER JOIN users keep ON keep.username = u.username AND keep.id < u.id'
        );

        $this->execute(
            'DELETE u FROM users u
             INNER JOIN users keep ON keep.username = u.username AND keep.id < u.id'
        );

        $this->table('users')
             ->addIndex(['username'], ['unique' => true, 'name' => 'uq_users_username'])
             ->update();
    }

    public function down(): void
    {
        $this->table('users')
             ->removeIndexByName('uq_users_username')
             ->update();
    }
}
