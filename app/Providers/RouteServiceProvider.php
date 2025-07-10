<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register bindings or services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap routes.
     */
    public function boot(): void
    {
        // If needed, define additional route loading here
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }
}
