<?php

namespace App\Contracts\Auth;

interface AuthenticateUserInterface
{
    /**
     * Authenticate a user with the given credentials
     *
     * @param array{email: string, password: string, remember?: bool} $credentials
     * @return bool
     */
    public function authenticate(array $credentials): bool;
}
