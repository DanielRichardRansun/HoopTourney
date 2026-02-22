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

    // Handle the request... (This will boot the app and providers)
    $app->handleRequest(Request::capture());

} catch (Throwable $e) {
    // Output error to stderr for Vercel logs and display on screen
    error_log('Boot Error: ' . $e->getMessage());
    
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
    }
    
    echo "<html><body style='font-family: sans-serif; padding: 2rem;'>";
    echo "<h1 style='color: #e53e3e;'>Boot Error (Vercel)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background: #f7fafc; padding: 1rem; overflow: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
}
