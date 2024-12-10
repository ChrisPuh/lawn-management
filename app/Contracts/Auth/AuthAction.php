<?php

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
