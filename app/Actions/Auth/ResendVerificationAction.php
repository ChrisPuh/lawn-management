<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\ResendVerificationActionInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

final class ResendVerificationAction implements ResendVerificationActionInterface
{
    /**
     * The number of attempts allowed.
     */
    private const ATTEMPTS_ALLOWED = 1;

    /**
     * The cooldown period in seconds.
     */
    private const COOLDOWN_SECONDS = 60;

    public function execute(): bool
    {
        if (! Auth::check()) {
            throw new AuthenticationException('User must be logged in.');
        }

        $user = Auth::user();

        // Don't send verification email if already verified
        if (! is_null($user->email_verified_at)) {
            return true;
        }

        $key = $this->getRateLimitKey();

        if (RateLimiter::tooManyAttempts($key, self::ATTEMPTS_ALLOWED)) {
            throw ValidationException::withMessages([
                'verification' => __('Please wait a moment before trying again.'),
            ]);
        }

        RateLimiter::hit($key, self::COOLDOWN_SECONDS);

        $user->sendEmailVerificationNotification();

        session()->flash('status', 'verification-link-sent');

        return true;
    }

    private function getRateLimitKey(): string
    {
        return 'resend-verification-'.Auth::id();
    }
}
