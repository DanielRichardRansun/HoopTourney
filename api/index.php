<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

define('LARAVEL_START', microtime(true));

try {
    // 1. Clear boot cache if it exists (leaked from local)
    $cacheFiles = [
        __DIR__.'/../bootstrap/cache/services.php',
        __DIR__.'/../bootstrap/cache/packages.php',
        __DIR__.'/../bootstrap/cache/config.php',
    ];
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    // 2. Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // 3. Bootstrap Laravel...
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // 4. Serverless Fixes
    $app->useStoragePath('/tmp/storage');
    $dirs = ['/tmp/storage/framework/views', '/tmp/storage/framework/cache', '/tmp/storage/framework/sessions', '/tmp/storage/logs'];
    foreach ($dirs as $dir) { if (!is_dir($dir)) { @mkdir($dir, 0755, true); } }

    // 5. MANUALLY BOOT for debugging
    $app->boot();

    // 6. Handle the request
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

    if (isset($app)) {
        echo "<div style='margin-top: 2rem; border-top: 1px dashed #feb2b2; padding-top: 1rem;'>";
        echo "<h4>Container State:</h4>";
        echo "<ul>";
        echo "<li><strong>View Bound?</strong> " . ($app->bound('view') ? 'Yes' : 'No') . "</li>";
        echo "<li><strong>Config Bound?</strong> " . ($app->bound('config') ? 'Yes' : 'No') . "</li>";
        echo "<li><strong>Loaded Providers:</strong> " . count($app->getLoadedProviders()) . "</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "</body></html>";
}
