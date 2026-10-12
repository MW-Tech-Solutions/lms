<?php

/**
 * Enterprise Dual-Audience LMS - Root Gateway
 *
 * This file handles web requests on shared hosting and server environments
 * where the Web Server DocumentRoot points to the project root instead of /public.
 */

// If request points directly to an existing asset inside /public, serve it
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/'
);

if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
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
