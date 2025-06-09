<?php

namespace App\Providers;
use Eludadev\Passage\Middleware\PassageAuthMiddleware;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(PassageAuthMiddleware::class, function ($app) {
        return new PassageAuthMiddleware(env('PASSAGE_APP_ID'));
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
