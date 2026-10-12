<?php

declare(strict_types=1);

/**
 * Enterprise Dual-Audience LMS - Root Gateway for Shared Hosting
 */

// If request is for run-setup.php, serve it directly
if (str_contains($_SERVER['REQUEST_URI'] ?? '', 'run-setup.php')) {
    require_once __DIR__ . '/run-setup.php';
    exit;
}

// Auto-create essential storage directories if missing from git pull
$requiredDirs = [
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/cache/data',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/logs',
    __DIR__ . '/bootstrap/cache',
];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// Auto-create .env from .env.example with valid APP_KEY if missing
if (!file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/.env.example')) {
    $exampleContent = file_get_contents(__DIR__ . '/.env.example');
    $generatedKey = 'base64:' . base64_encode(random_bytes(32));
    $exampleContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $generatedKey, $exampleContent);
    @file_put_contents(__DIR__ . '/.env', $exampleContent);
}

// 1. Direct gateway to public/index.php if present
if (file_exists(__DIR__ . '/public/index.php')) {
    require_once __DIR__ . '/public/index.php';
    exit;
}

// 2. Direct fallback bootstrap
define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->handleRequest(\Illuminate\Http\Request::capture());
