<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\LogoutActionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class LogoutAction implements LogoutActionInterface
{
    /**
     * Execute the basic logout action.
     */
    public function execute(): void
    {
        Auth::guard('web')->logout();

        $currentRequest = app('request');
        if ($currentRequest->hasSession()) {
            $currentRequest->session()->invalidate();
            $currentRequest->session()->regenerateToken();
        }
    }

    /**
     * Handle the logout request and return a response.
     */
    public function handleRequest(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
