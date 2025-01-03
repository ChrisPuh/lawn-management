<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CacheHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $options = ''): Response
    {
        $response = $next($request);

        if (str_contains($options, 'public')) {
            $response->headers->set('Cache-Control', 'public, max-age=3600');
        }

        return $response;
    }
}
