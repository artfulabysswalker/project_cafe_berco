<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\GuestModeMiddleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RestoreGuestMiddleware;
use App\Http\Middleware\RestrictToLocalNetwork;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            RestoreGuestMiddleware::class,
        ]);

        $middleware->alias([
            'customer' => CustomerMiddleware::class,
            'admin.staff' => AdminMiddleware::class,
            'is_admin' => IsAdmin::class,
            'guest.mode' => GuestModeMiddleware::class,
            'restore.guest' => RestoreGuestMiddleware::class,
            'local.network' => RestrictToLocalNetwork::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
