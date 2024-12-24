<?php

declare(strict_types=1);

namespace App\Contracts\Auth;

interface ResendVerificationActionInterface extends AuthAction
{
    /**
     * Resend the verification email.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function execute(): bool;
}
