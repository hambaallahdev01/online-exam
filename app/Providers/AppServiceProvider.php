<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        // Rate Limiter: Login (Anti-Bruteforce & Anti-DDoS)
        RateLimiter::for('login', function (Request $request) {
            $ip = $request->header('CF-Connecting-IP', $request->ip());
            return Limit::perMinute(5)->by($ip . '|' . $request->input('email'));
        });

        // Rate Limiter: School Registration
        RateLimiter::for('register-school', function (Request $request) {
            $ip = $request->header('CF-Connecting-IP', $request->ip());
            return Limit::perHour(5)->by($ip);
        });

        // Rate Limiter: Student Exam API (Autosave & Payload)
        RateLimiter::for('api-exam', function (Request $request) {
            $ip = $request->header('CF-Connecting-IP', $request->ip());
            $userId = optional($request->user())->id ?? 'guest';
            return Limit::perMinute(60)->by($userId . '|' . $ip);
        });
    }
}
