<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\AuthenticateUserInterface;
use Illuminate\Support\Facades\Auth;

final class AuthenticateUser implements AuthenticateUserInterface
{
    /**
     * Authenticate a user with the given credentials
     *
     * @param  array{email: string, password: string, remember?: bool}  $credentials
     */
    public function authenticate(array $credentials): bool
    {
        $remember = $credentials['remember'] ?? false;

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            session()->regenerate();

            return true;
        }

        return false;
    }
}
