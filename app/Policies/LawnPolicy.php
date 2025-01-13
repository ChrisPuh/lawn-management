<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lawn;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class LawnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any lawns.
     */
    public function viewAny(): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the lawn.
     */
    public function view(User $user, Lawn $lawn): Response
    {
        return $user->id === $lawn->user_id
            ? Response::allow()
            : Response::deny('You do not own this lawn.');
    }

    /**
     * Determine whether the user can create lawns.
     */
    public function create(): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the lawn.
     */
    public function update(User $user, Lawn $lawn): Response
    {
        return $user->id === $lawn->user_id
            ? Response::allow()
            : Response::deny('You do not own this lawn.');
    }

    /**
     * Determine whether the user can delete the lawn.
     */
    public function delete(User $user, Lawn $lawn): Response
    {
        return $user->id === $lawn->user_id
            ? Response::allow()
            : Response::deny('You do not own this lawn.');
    }

    /**
     * Determine whether the user can restore the lawn.
     */
    public function restore(User $user, Lawn $lawn): Response
    {
        return $user->id === $lawn->user_id
            ? Response::allow()
            : Response::deny('You do not own this lawn.');
    }

    /**
     * Determine whether the user can permanently delete the lawn.
     */
    public function forceDelete(User $user, Lawn $lawn): Response
    {
        return $user->id === $lawn->user_id
            ? Response::allow()
            : Response::deny('You do not own this lawn.');
    }

    /**
     * Determine whether the user can manage mowing records for the lawn.
     */
    public function manageMowingRecords(User $user, Lawn $lawn): Response
    {
        return $user->id === $lawn->user_id
            ? Response::allow()
            : Response::deny('You do not own this lawn.');
    }
}
