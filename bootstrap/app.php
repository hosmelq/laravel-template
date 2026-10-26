<?php

declare(strict_types=1);

use App\Http\Middleware\DecodeSqids;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware
            ->alias([
                'admin' => EnsureAdmin::class,
                'sqids' => DecodeSqids::class,
            ])
            ->redirectUsersTo('/')
            ->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->create();
