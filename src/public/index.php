<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use app\controllers\LoginController;
use app\controllers\LogoutController;
use app\controllers\ProfileController;
use app\controllers\RegisterController;
use app\controllers\UpdateProfileController;
use app\core\Application;
use app\core\middleware\AuthMiddleware;
use app\core\middleware\SessionMiddleware;

$config = [
    'root_path' => dirname(__DIR__),
    'database' => [
        'dsn' => $_ENV['DB_DSN'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

$app = new Application($config);

$app
    ->get(
        '/login',
        [
            'class' => LoginController::class,
            'action' => 'login',
            'middlewares' => [SessionMiddleware::class]
        ]
    )
    ->post(
        '/login',
        [
            'class' => LoginController::class,
            'action' => 'handleLogin',
        ]
    )
    ->get(
        '/register',
        [
            'class' => RegisterController::class,
            'action' => 'register',
            'middlewares' => [SessionMiddleware::class]
        ]
    )
    ->post(
        '/register',
        [
            'class' => RegisterController::class,
            'action' => 'handleRegister',
        ]
    )
    ->get(
        '/profile',
        [
            'class' => ProfileController::class,
            'action' => 'profile',
            'middlewares' => [AuthMiddleware::class]
        ]
    )
    ->post(
        '/upload-image',
        [
            'class' => UpdateProfileController::class,
            'action' => 'updateAvatar'
        ],
    )
    ->post(
        '/update-profile',
        [
            'class' => UpdateProfileController::class,
            'action' => 'updateProfile',
        ],
    )
    ->post(
        '/update-password',
        [
            'class' => UpdateProfileController::class,
            'action' => 'updatePassword',
        ],
    )
    ->get(
        '/logout',
        [
            'class' => LogoutController::class,
            'action' => 'logout',
        ]
    )
    ->run();
