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
        // Force HTTPS in production
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Set PostgreSQL timeouts for production
        if (config('database.default') === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("SET lock_timeout = '30s'");
            \Illuminate\Support\Facades\DB::statement("SET statement_timeout = '60s'");
        }
    }
}
