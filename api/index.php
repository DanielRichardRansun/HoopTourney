<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    // 2. Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // 3. Bootstrap Laravel...
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // 4. Vercel / Serverless Fix: Force storage to /tmp
    // This is the most crucial part for serverless environments
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
        mkdir($storagePath . '/framework/views', 0755, true);
        mkdir($storagePath . '/framework/cache', 0755, true);
        mkdir($storagePath . '/framework/sessions', 0755, true);
        mkdir($storagePath . '/logs', 0755, true);
    }
    $app->useStoragePath($storagePath);

    // 5. Handle the request (Kernel will handle the boot properly)
    $app->handleRequest(Request::capture());

} catch (Throwable $e) {
    // Output error to stderr for Vercel logs and display on screen
    error_log('Boot Error: ' . $e->getMessage());
    
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
    }
    
    echo "<html><body style='font-family: sans-serif; padding: 2rem; background: #fff5f5;'>";
    echo "<h1 style='color: #c53030;'>Critical Boot Error (Vercel)</h1>";
    echo "<p style='font-size: 1.2rem;'><strong>Original Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    
    echo "<div style='background: #fff; border: 1px solid #feb2b2; padding: 1rem; border-radius: 4px;'>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='overflow: auto; white-space: pre-wrap;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
    echo "</body></html>";
}
