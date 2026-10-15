<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\Message;
use App\Models\User;
use App\Policies\BusinessPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach (glob(app_path('Helpers').'/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- Policies ---
        Gate::policy(Business::class, BusinessPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // --- Showcase rate limiter ---
        RateLimiter::for('showcase', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip() ?: '127.0.0.1');
        });

        // --- Background image upload rate limiter ---
        RateLimiter::for('image-uploads', function (object $job) {
            return Limit::perMinute(30);
        });

        // --- Unread inbox count for the navigation bell ---
        View::composer('layouts.navigation', function ($view) {
            $view->with('unreadInboxCount', auth()->check()
                ? Message::where('recipient_id', auth()->id())->whereNull('read_at')->count()
                : 0);
        });
    }
}
