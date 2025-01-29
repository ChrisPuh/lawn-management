<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle($request, Closure $next)
    {
        // Debuggen Sie die Umleitung
        \Log::info('ForceHttps Middleware', [
            'secure' => $request->secure(),
            'environment' => App::environment(),
            'url' => $request->fullUrl()
        ]);

        // Temporär nur loggen, nicht umleiten
        if (!$request->secure() && App::environment('production')) {
            \Log::warning('Potential HTTPS redirect', [
                'url' => $request->fullUrl()
            ]);

        }
//        if (!$request->secure() && App::environment('production')) {
//            return redirect()->secure($request->getRequestUri());
//        }

        return $next($request);
    }
}
