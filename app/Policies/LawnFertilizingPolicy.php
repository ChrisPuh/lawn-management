<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LawnFertilizing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @property-read \App\Models\Lawn $lawn
 */
final class LawnFertilizingPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, LawnFertilizing $lawnFertilizing): bool
    {
        return $user->id === $lawnFertilizing->lawn->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, LawnFertilizing $lawnFertilizing): bool
    {
        return $user->id === $lawnFertilizing->lawn->user_id;
    }

    public function delete(User $user, LawnFertilizing $lawnFertilizing): bool
    {
        return $user->id === $lawnFertilizing->lawn->user_id;
    }

    public function restore(User $user, LawnFertilizing $lawnFertilizing): bool
    {
        return $user->id === $lawnFertilizing->lawn->user_id;
    }

    public function forceDelete(User $user, LawnFertilizing $lawnFertilizing): bool
    {
        return $user->id === $lawnFertilizing->lawn->user_id;
    }
}
