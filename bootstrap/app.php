<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\ServerTiming;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // --- Timing of every response, for measuring the live site ---
        $middleware->prepend(ServerTiming::class);

        $middleware->alias([
            'admin' => IsAdmin::class,
        ]);

        // --- Redirect unauthenticated users to the home page ---
        $middleware->redirectGuestsTo('/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
