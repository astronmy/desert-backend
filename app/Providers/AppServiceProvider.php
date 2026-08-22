<?php

namespace App\Providers;

use App\Contracts\GuestNotifier;
use App\Services\Notifications\LogGuestNotifier;
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
        //
    }
}
