<?php

namespace App\Providers;

use App\Contracts\GuestNotifier;
use App\Models\User;
use App\Services\Notifications\LogGuestNotifier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GuestNotifier::class, LogGuestNotifier::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('permission', function (User $user, string $slug): bool {
            return $user->canPermission($slug);
        });

        View::composer('layouts.admin', function (): void {
            auth()->user()?->loadMissing('role.permissions');
        });
    }
}
