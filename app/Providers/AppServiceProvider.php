<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\ResendVerificationAction;
use App\Contracts\Auth\AuthenticateUserInterface;
use App\Contracts\Auth\LogoutActionInterface;
use App\Contracts\Auth\RegisterUserInterface;
use App\Contracts\Auth\ResendVerificationActionInterface;
use App\Services\Cookies\CookieConsentService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CookieConsentService::class);


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
        // Optional: Wenn die Config nicht existiert, publiziere sie
        if (! file_exists(config_path('navigation.php'))) {
            $this->publishes([
                __DIR__.'/../config/navigation.php' => config_path('navigation.php'),
            ], 'config');
        }
    }
}
