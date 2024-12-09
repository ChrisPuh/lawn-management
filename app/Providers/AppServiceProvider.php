<?php

namespace App\Providers;

use App\Actions\Auth\AuthenticateUser;
use App\Contracts\Auth\AuthenticateUserInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticateUserInterface::class, AuthenticateUser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
