<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\LogoutActionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutAction implements LogoutActionInterface
{
    /**
     * Execute the basic logout action.
     *
     * @return void
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
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleRequest(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
