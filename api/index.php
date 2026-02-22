<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ──────────────────────────────────────────────────────────────
// Vercel Serverless Bootstrap for Laravel 11
// ──────────────────────────────────────────────────────────────
// Vercel's filesystem is read-only except /tmp.
// We redirect all writable paths there before Laravel boots.
// ──────────────────────────────────────────────────────────────

$storagePath = '/tmp/storage';

// Create all required storage directories
foreach ([
    $storagePath,
    $storagePath . '/framework',
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// ──────────────────────────────────────────────────────────────
// Force cache paths to /tmp BEFORE anything Laravel-related loads
// These env vars are read by Application::normalizeCachePath()
// ──────────────────────────────────────────────────────────────
$_ENV['APP_CONFIG_CACHE']   = '/tmp/config.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE']   = '/tmp/routes.php';
$_ENV['APP_EVENTS_CACHE']   = '/tmp/events.php';

// Also set in $_SERVER and putenv for maximum compatibility
foreach (['APP_CONFIG_CACHE', 'APP_SERVICES_CACHE', 'APP_PACKAGES_CACHE', 'APP_ROUTES_CACHE', 'APP_EVENTS_CACHE'] as $key) {
    $_SERVER[$key] = $_ENV[$key];
    putenv("{$key}={$_ENV[$key]}");
}

// Register the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Tell Laravel to use /tmp/storage as the storage path
$app->useStoragePath($storagePath);

// Set config overrides AFTER config is loaded but BEFORE providers boot
// This ensures ViewServiceProvider can find the compiled view path
$app->beforeBootstrapping(
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
    function ($app) use ($storagePath) {
        config([
            'view.compiled' => $storagePath . '/framework/views',
            'view.paths' => [resource_path('views')],
            'cache.stores.file.path' => $storagePath . '/framework/cache/data',
            'session.files' => $storagePath . '/framework/sessions',
            'logging.channels.single.path' => $storagePath . '/logs/laravel.log',
            'logging.channels.daily.path' => $storagePath . '/logs/laravel.log',
        ]);
    }
);

// ──────────────────────────────────────────────────────────────
// Handle the Request (Laravel 11 native method)
// ──────────────────────────────────────────────────────────────
$app->handleRequest(Request::capture());
