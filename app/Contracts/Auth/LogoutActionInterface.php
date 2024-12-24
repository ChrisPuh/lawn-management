<?php

declare(strict_types=1);

namespace App\Contracts\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface LogoutActionInterface extends AuthAction
{
    /**
     * Log the user out.
     */
    public function execute(): void;

    /**
     * Handle the logout request and return a response.
     */
    public function handleRequest(Request $request): Response;
}
