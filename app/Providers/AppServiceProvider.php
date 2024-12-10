<?php

namespace App\Providers;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\{LogoutAction, ResendVerificationAction};
use App\Contracts\Auth\AuthenticateUserInterface;
use App\Contracts\Auth\RegisterUserInterface;
use App\Contracts\Auth\{LogoutActionInterface, ResendVerificationActionInterface};
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
        $this->app->bind(ResendVerificationActionInterface::class, ResendVerificationAction::class);
        $this->app->bind(LogoutActionInterface::class, LogoutAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
