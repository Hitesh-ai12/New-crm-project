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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->withProviders([
        Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,

        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class, // ✅ Add this line

        App\Providers\BroadcastServiceProvider::class,

        App\Providers\RouteServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
    ])

    ->create();
