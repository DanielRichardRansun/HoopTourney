<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    // Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel...
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // Vercel / Serverless Fix: Force storage to /tmp
    $app->useStoragePath('/tmp/storage');
    
    // Ensure necessary directories exist in /tmp
    $dirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // Set view cache path specifically
    config(['view.compiled' => '/tmp/storage/framework/views']);

    // Handle the request...
    $app->handleRequest(Request::capture());

} catch (Throwable $e) {
    // Output error to stderr for Vercel logs and display on screen
    error_log('Boot Error: ' . $e->getMessage());
    
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>Boot Error (Vercel)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
