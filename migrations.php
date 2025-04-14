<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\core\Application;

$config = [
    'root_path' => __DIR__,
    'database' => [
        'dsn' => $_ENV['DB_DSN'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

$app = new Application($config);

/** @var 'up'|'down' $command */
$command = $argv[1] ?? null;
switch ($command) {
    case 'up':
        $app::$DATABASE->applyMigrations();
        break;
    case 'down':
        $app::$DATABASE->revertMigration();
        break;
}
