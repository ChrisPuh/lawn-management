<?php

declare(strict_types=1);

namespace App\Http\Middleware\Cookies;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckCookieConsent
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('cookie-consent.enabled')) {
            return $next($request);
        }

        $cookieName = config('cookie-consent.cookie_name');
        $hasConsent = $request->cookie($cookieName);

        if ($hasConsent) {
            session(['cookie_consent' => true]);
        }

        if (! $hasConsent && $this->routeRequiresConsent($request)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cookie consent required',
                    'requires_consent' => true,
                ], 403);
            }

            return redirect()->route('cookie-policy')
                ->with('warning', 'Bitte akzeptieren Sie die Cookies, um diese Funktion zu nutzen.');
        }

        return $next($request);
    }

    /**
     * Check if the current route requires cookie consent.
     */
    private function routeRequiresConsent(Request $request): bool
    {
        $requiresConsent = [
            'analytics/*',
            'tracking/*',
        ];

        return array_any($requiresConsent, fn ($path) => $request->is($path));

    }
}
