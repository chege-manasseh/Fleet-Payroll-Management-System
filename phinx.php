<?php

require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$env = static function (string $key, $default = null) {
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    return $_ENV[$key] ?? $default;
};

return
    [
        'paths' => [
            'migrations' => '%%PHINX_CONFIG_DIR%%/src/Storage/db/migrations/',
            'seeds' => '%%PHINX_CONFIG_DIR%%/src/Storage/db/seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host'    => $env('DB_HOST', '127.0.0.1'),
                'name'    => $env('DB_DATABASE'),
                'user'    => $env('DB_USERNAME'),
                'pass'    => $env('DB_PASSWORD', ''),
                'port'    => $env('DB_PORT', '3306'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_0900_ai_ci',
            ],
            'development' => [
                'adapter' => 'mysql',
                'host'    => $env('DB_HOST', '127.0.0.1'),
                'name'    => $env('DB_DATABASE'),
                'user'    => $env('DB_USERNAME'),
                'pass'    => $env('DB_PASSWORD', ''),
                'port'    => $env('DB_PORT', '3306'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_0900_ai_ci',
            ],
            'testing' => [
                'adapter' => 'mysql',
                'host'    => $env('DB_HOST', '127.0.0.1'),
                'name'    => $env('DB_DATABASE'),
                'user'    => $env('DB_USERNAME'),
                'pass'    => $env('DB_PASSWORD', ''),
                'port'    => $env('DB_PORT', '3306'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_0900_ai_ci',
            ]
        ],
        'version_order' => 'creation'
    ];
