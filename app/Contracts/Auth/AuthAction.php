<?php

declare(strict_types=1);

namespace App\Contracts\Auth;

interface AuthAction
{
    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute();
}
