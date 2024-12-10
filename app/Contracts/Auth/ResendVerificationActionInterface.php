<?php

namespace App\Contracts\Auth;


interface ResendVerificationActionInterface extends AuthAction
{
    /**
     * Resend the verification email.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return bool
     */
    public function execute(): bool;
}
