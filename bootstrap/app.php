<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Exempt logout from CSRF verification.
        // WHY: The CSRF token lives inside the session. When the session
        // expires (after SESSION_LIFETIME minutes of inactivity), the token
        // becomes invalid. A user who was idle then clicks "Keluar" gets a
        // 419 Page Expired before the controller is ever reached.
        // CONSEQUENCE: A CSRF-forced logout is harmless — the worst an
        // attacker can do is log the user out. This is acceptable.
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        // Trust all proxies when behind Nginx reverse proxy.
        // Uses env() instead of app()->environment() because the container's
        // 'env' binding is not yet registered at middleware-configuration time.
        if (env('APP_ENV') === 'production') {
            $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();