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
        if (
            $this->app->environment('production') ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ||
            isset($_SERVER['VERCEL']) ||
            isset($_ENV['VERCEL']) ||
            getenv('VERCEL')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
