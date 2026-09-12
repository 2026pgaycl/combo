<?php

declare(strict_types=1);

use App\Core\Env;

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

Env::load(__DIR__ . '/../.env');

$isProd = Env::get('APP_ENV', 'production') === 'production';
error_reporting(E_ALL);
ini_set('display_errors', $isProd ? '0' : '1');
date_default_timezone_set('Asia/Kuala_Lumpur');

session_name(Env::get('SESSION_NAME', 'combo_session'));
session_start();
