<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LawnAerating;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @property-read \App\Models\Lawn $lawn
 */
final class LawnAeratingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LawnAerating $lawnAerating): bool
    {
        return $user->id === $lawnAerating->lawn->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LawnAerating $lawnAerating): bool
    {
        return $user->id === $lawnAerating->lawn->user_id;
    }

    public function delete(User $user, LawnAerating $lawnAerating): bool
    {
        return $user->id === $lawnAerating->lawn->user_id;
    }

    public function restore(User $user, LawnAerating $lawnAerating): bool
    {
        return $user->id === $lawnAerating->lawn->user_id;
    }

    public function forceDelete(User $user, LawnAerating $lawnAerating): bool
    {
        return $user->id === $lawnAerating->lawn->user_id;
    }
}
