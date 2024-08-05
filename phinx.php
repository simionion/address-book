<?php

require 'vendor/autoload.php';

function loadEnvFile($filePath): array
{
    if (file_exists($filePath)) {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname($filePath), basename($filePath));
        $dotenv->load();
        return $_ENV;
    }
    return [];
}

// Load the base .env file first
$baseConfig = loadEnvFile(__DIR__ . '/.env');


// Load environment-specific .env files
$envFiles = ['development', 'production', 'testing'];
$configs = [];

foreach ($envFiles as $env) {
    $filePath = __DIR__ . "/.env.$env";
    $configs[$env] = loadEnvFile($filePath);
}

// Determine the application environment
$appEnv = $baseConfig['APP_ENV'] ?? 'development';

// If APP_ENV matches any environment, override that specific environment configuration with the base configuration
if (array_key_exists($appEnv, $configs)) {
    $configs[$appEnv] = array_merge($configs[$appEnv], $baseConfig);
}

function getEnvironmentConfig($config): array
{
    return [
        'adapter' => $config['DB_ADAPTER'] ?? null,
        'host' => $config['DB_HOST'] ?? null,
        'name' => $config['DB_NAME'] ?? null,
        'user' => $config['DB_USER'] ?? null,
        'pass' => $config['DB_PASS'] ?? null,
        'port' => $config['DB_PORT'] ?? null,
        'charset' => $config['DB_CHARSET'] ?? null,
    ];
}

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $appEnv,
        'production' => getEnvironmentConfig($configs['production']),
        'development' => getEnvironmentConfig($configs['development']),
        'testing' => getEnvironmentConfig($configs['testing']),
    ],
    'version_order' => 'creation'
];
