<?php

namespace App\Contracts\Auth;

use App\Models\User;

interface RegisterUserInterface
{
    /**
     * Register a new user with the given data.
     *
     * @param array{name: string, email: string, password: string} $data
     * @return User
     */
    public function register(array $data): User;
}
