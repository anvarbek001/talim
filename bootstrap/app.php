<?php

use App\Http\Middleware\EnsureLessonsEnabled;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'lessons.enabled' => EnsureLessonsEnabled::class,
        ]);

        // Click.uz calls these directly (no session/CSRF token of ours).
        $middleware->validateCsrfTokens(except: [
            'payments/click/prepare',
            'payments/click/complete',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
