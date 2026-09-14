<?php

use App\Http\Middleware\CaptureAnalyticsEvent;
use App\Http\Middleware\HandleRedirects;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
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
        $middleware->append(SecurityHeaders::class);
        $middleware->append(HandleRedirects::class);

        $middleware->web(append: [
            SetLocale::class,
            CaptureAnalyticsEvent::class,
        ]);

        $middleware->alias([
            'set.locale' => SetLocale::class,
            'analytics.capture' => CaptureAnalyticsEvent::class,
            'security.headers' => SecurityHeaders::class,
            'handle.redirects' => HandleRedirects::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
