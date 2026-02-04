<?php

namespace App\Providers;

use App\Session\DatabaseSessionHandler;
use Illuminate\Support\ServiceProvider;

/**
 * Session Service Provider
 * 
 * Register custom database session handler tanpa user_agent tracking
 */
class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['session']->extend('database', function ($app) {
            $connection = $app['config']['session.connection'];
            $table = $app['config']['session.table'];
            $lifetime = $app['config']['session.lifetime'];

            return new DatabaseSessionHandler(
                $app['db']->connection($connection),
                $table,
                $lifetime,
                $app
            );
        });
    }
}
