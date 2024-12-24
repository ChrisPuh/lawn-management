<?php

declare(strict_types=1);

namespace App\Contracts\Auth;

interface AuthenticateUserInterface
{
    /**
     * Authenticate a user with the given credentials
     *
     * @param  array{email: string, password: string, remember?: bool}  $credentials
     */
    public function authenticate(array $credentials): bool;
}
