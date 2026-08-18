<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ResetPasswordTokens extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('password_reset_tokens', [
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
              ->addColumn('token_hash', 'string', ['limit' => 256, 'null' => true])
              ->addColumn('expires_at', 'timestamp', ['null' => false])
              ->addColumn('used_at', 'timestamp', ['null' => true, 'default' => null])
              ->addColumn('created_at', 'timestamp', [
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
            ])
              ->addColumn('delivery_channel', 'enum', [
                'values' => ['email', 'phone'],
                'null' => false,
              ])
              ->addColumn('failed_attempts', 'tinyinteger', [
                'null' => false,
                'default' => 0,
              ])
              ->addColumn('is_used', 'boolean', [
                'null' => true,
                'default' => false,
              ])
              ->addColumn('code_hash', 'string', ['limit' => 255, 'null' => true])
              ->addIndex(['token_hash'], ['unique' => true, 'name' => 'uq_reset_hash'])
              ->addIndex(['code_hash'], ['unique' => true, 'name' => 'code_hash'])
              ->addForeignKey('user_id', 'users', 'id', [
                'constraint' => 'fk_password_reset_token',
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
              ])
              ->create();
    }
}
