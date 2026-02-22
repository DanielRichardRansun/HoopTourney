<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');

            // Vercel / Serverless Fix: Redirect paths to /tmp since the filesystem is read-only
            $viewPath = '/tmp/framework/views';
            if (!is_dir($viewPath)) {
                mkdir($viewPath, 0755, true);
            }
            config(['view.compiled' => $viewPath]);
        }
    }
}
