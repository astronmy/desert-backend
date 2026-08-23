<?php

namespace App\Providers;

use App\Contracts\GuestNotifier;
use App\Contracts\OneSignalServiceInterface;
use App\Models\User;
use App\Services\Notifications\LogGuestNotifier;
use App\Services\Notifications\OneSignalService;
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
        $this->app->bind(OneSignalServiceInterface::class, OneSignalService::class);
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
