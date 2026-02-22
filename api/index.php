<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

define('LARAVEL_START', microtime(true));

try {
    // Check if APP_KEY is set (Vercel Env Var)
    if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
        throw new Exception("APP_KEY is not set in Vercel Environment Variables.");
    }

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

    // Handle the request directly using the Kernel to see the true exception
    $kernel = $app->make(Kernel::class);
    $request = Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

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

    echo "<div style='margin-top: 2rem; border-top: 1px dashed #feb2b2; padding-top: 1rem;'>";
    echo "<h4>Environment Check:</h4>";
    echo "<ul>";
    echo "<li><strong>PHP Version:</strong> " . PHP_VERSION . "</li>";
    echo "<li><strong>APP_KEY set?</strong> " . (empty(getenv('APP_KEY')) ? 'No' : 'Yes (starts with ' . substr(getenv('APP_KEY'), 0, 10) . '...)') . "</li>";
    echo "<li><strong>APP_ENV:</strong> " . getenv('APP_ENV') . "</li>";
    echo "</ul>";
    echo "</div>";
    echo "</body></html>";
}
