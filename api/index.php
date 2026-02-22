<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    // Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (Exception $e) {
    // Output error to stderr for Vercel logs
    error_log('Boot Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo "<h1>Boot Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
