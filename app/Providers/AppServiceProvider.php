<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\User;
use App\Policies\BusinessPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load custom helpers
        foreach (glob(app_path('Helpers') . '/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Register policies
        Gate::policy(Business::class, BusinessPolicy::class);
        Gate::policy(User::class, UserPolicy::class); // ✅ ADDED

        // ✅ Register showcase rate limiter
        RateLimiter::for('showcase', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip() ?: '127.0.0.1');
        });
    }
}
