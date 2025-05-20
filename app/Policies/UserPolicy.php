<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view user panel');
    }

    /**
     * Determine whether the user can create a user.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can update a user.
     */
    public function update(User $user, User $targetUser): bool
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can delete a user.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasRole('ADMIN');
    }
}

