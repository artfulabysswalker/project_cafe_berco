<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function ($middleware) {

    $middleware->alias([
        'customer' => \App\Http\Middleware\CustomerMiddleware::class,
        'admin.staff' => \App\Http\Middleware\AdminMiddleware::class,
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
        'guest.mode' => \App\Http\Middleware\GuestModeMiddleware::class,
        'restore.guest' => \App\Http\Middleware\RestoreGuestMiddleware::class,
    ]);

})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


    
