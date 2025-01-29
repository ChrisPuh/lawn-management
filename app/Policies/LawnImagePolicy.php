<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lawn;
use App\Models\LawnImage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class LawnImagePolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, LawnImage $lawnImage): bool
    {
        /** @var Lawn $lawn */
        $lawn = $lawnImage->lawn;

        return $user->id === $lawn->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, LawnImage $lawnImage): bool
    {
        /** @var Lawn $lawn */
        $lawn = $lawnImage->lawn;

        return $user->id === $lawn->user_id;
    }

    public function delete(User $user, LawnImage $lawnImage): bool
    {
        /** @var Lawn $lawn */
        $lawn = $lawnImage->lawn;

        return $user->id === $lawn->user_id;
    }

    public function restore(User $user, LawnImage $lawnImage): bool
    {
        /** @var Lawn $lawn */
        $lawn = $lawnImage->lawn;

        return $user->id === $lawn->user_id;
    }

    public function forceDelete(User $user, LawnImage $lawnImage): bool
    {
        /** @var Lawn $lawn */
        $lawn = $lawnImage->lawn;

        return $user->id === $lawn->user_id;
    }
}
