<?php

namespace App\Services\Cookies;

use Illuminate\Support\Facades\Cookie;

class CookieConsentService
{
    public function hasConsent(): bool
    {
        return session('cookie_consent', false)
            || Cookie::has(config('cookie-consent.cookie_name'));
    }

    public function grantConsent(): void
    {
        $minutes = config('cookie-consent.cookie_lifetime', 525600); // Default: 1 Jahr

        Cookie::queue(
            config('cookie-consent.cookie_name'),
            true,
            $minutes
        );

        session(['cookie_consent' => true]);
    }

    public function revokeConsent(): void
    {
        Cookie::queue(
            Cookie::forget(config('cookie-consent.cookie_name'))
        );

        session()->forget('cookie_consent');
    }
}
