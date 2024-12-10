<?php

namespace App\Contracts\Auth;

use App\Contracts\Auth\AuthAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface LogoutActionInterface extends AuthAction
{
    /**
     * Log the user out.
     *
     * @return void
     */
    public function execute(): void;

    /**
     * Handle the logout request and return a response.
     */
    public function handleRequest(Request $request): Response;
}
