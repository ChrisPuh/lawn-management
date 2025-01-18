<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LawnScarifying;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @property-read \App\Models\Lawn $lawn
 */
final class LawnScarifyingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LawnScarifying $lawnScarifying): bool
    {
        return $user->id === $lawnScarifying->lawn->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LawnScarifying $lawnScarifying): bool
    {
        return $user->id === $lawnScarifying->lawn->user_id;
    }

    public function delete(User $user, LawnScarifying $lawnScarifying): bool
    {
        return $user->id === $lawnScarifying->lawn->user_id;
    }

    public function restore(User $user, LawnScarifying $lawnScarifying): bool
    {
        return $user->id === $lawnScarifying->lawn->user_id;
    }

    public function forceDelete(User $user, LawnScarifying $lawnScarifying): bool
    {
        return $user->id === $lawnScarifying->lawn->user_id;
    }
}
