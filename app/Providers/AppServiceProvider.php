<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate Limiter untuk Login & Register (Max 5x per menit)
        RateLimiter::for('login-register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate Limiter untuk Komentar (Max 2x per menit)
        RateLimiter::for('comments', function (Request $request) {
            return Limit::perMinute(2)->by($request->user()?->id ?: $request->ip());
        });
    }
}
