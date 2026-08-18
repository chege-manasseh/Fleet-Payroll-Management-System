<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RefreshTokens extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('refresh_tokens', [
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
                'null' => false,
              ])
              ->addColumn('token_hash', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('expires_at', 'datetime', ['null' => false])
              ->addColumn('revoked_at', 'datetime', ['null' => true, 'default' => null])
              ->addColumn('created_at', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
              ])
              ->addColumn('is_revoked', 'boolean', [
                'null' => true,
                'default' => false,
              ])
              ->addColumn('parent_token_hash', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('root_token_hash', 'string', ['limit' => 255, 'null' => true])
              ->addForeignKey('user_id', 'users', 'id', [
                'constraint' => 'fk_refresh_tokens_user',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
              ])
              ->create();
    }
}
