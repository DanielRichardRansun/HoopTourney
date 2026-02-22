<?php

use Illuminate\Http\Request;

// 1. Define Laravel Start
define('LARAVEL_START', microtime(true));

// 2. Setup /tmp storage early
// Serverless environments like Vercel have a read-only filesystem, 
// so we must use /tmp for all writable operations.
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0755, true);
    @mkdir($storagePath . '/framework/views', 0755, true);
    @mkdir($storagePath . '/framework/cache', 0755, true);
    @mkdir($storagePath . '/framework/sessions', 0755, true);
    @mkdir($storagePath . '/logs', 0755, true);
}

// 3. Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// 4. Load the Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';

// 5. Apply Vercel Overrides early
$app->useStoragePath($storagePath);

// Force the view compiled path to /tmp before any view service tries to use it
$app->afterResolving('view', function ($view) use ($storagePath) {
    config(['view.compiled' => $storagePath . '/framework/views']);
});

// 6. Handle the Request
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    // Robust error reporting for Vercel Logs
    error_log('Critical Boot Error: ' . $e->getMessage());
    
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
    }
    
    echo "<h1>Critical Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
