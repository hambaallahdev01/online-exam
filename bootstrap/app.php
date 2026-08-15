<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\RestoreCloudflareRealIp;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocaleFromIp;
use App\Http\Middleware\SetTenantLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);

        $middleware->web(append: [
            RestoreCloudflareRealIp::class,
            SetLocaleFromIp::class,
            SetTenantLocale::class,
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->is('*/api/*'),
        );
    })->create();
