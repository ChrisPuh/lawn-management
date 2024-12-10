<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\RegisterUserInterface;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction implements RegisterUserInterface
{
    /**
     * Register a new user with the given data.
     *
     * @inheritDoc
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}
