<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LawnImage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @property-read \App\Models\Lawn $lawn
 */
final class LawnImagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LawnImage $lawnImage): bool
    {
        return $user->id === $lawnImage->lawn->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LawnImage $lawnImage): bool
    {
        return $user->id === $lawnImage->lawn->user_id;
    }

    public function delete(User $user, LawnImage $lawnImage): bool
    {
        return $user->id === $lawnImage->lawn->user_id;
    }

    public function restore(User $user, LawnImage $lawnImage): bool
    {
        return $user->id === $lawnImage->lawn->user_id;
    }

    public function forceDelete(User $user, LawnImage $lawnImage): bool
    {
        return $user->id === $lawnImage->lawn->user_id;
    }
}
