<?php

namespace App\Providers;

use App\PaymentPlataformResolver\PaymentPlatformResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentPlatformResolver::class, function ($app) {
            return new PaymentPlatformResolver();
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
