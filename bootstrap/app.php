<?php

declare(strict_types=1);

use App\Http\Middleware\Cookies\CheckCookieConsent;
use App\Http\Middleware\ForceHttps;
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
            'cache.headers' => Illuminate\Http\Middleware\SetCacheHeaders::class,
            'cookie.consent' => CheckCookieConsent::class,
        ]);

        // Add HTTPS middleware
        // $middleware->appendToGroup('web', ForceHttps::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
