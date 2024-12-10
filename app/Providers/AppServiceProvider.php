<?php

namespace App\Providers;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\RegisterUserAction;
use App\Contracts\Auth\AuthenticateUserInterface;
use App\Contracts\Auth\RegisterUserInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticateUserInterface::class, AuthenticateUser::class);
        $this->app->bind(RegisterUserInterface::class, RegisterUserAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
