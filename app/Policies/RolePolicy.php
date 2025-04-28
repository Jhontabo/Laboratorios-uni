<?php

namespace App\Policies;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view role panel');
    }

    /**
     * Determine whether the user can view a specific role.
     */
    public function view(User $user, Rol $rol): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view role');
    }

    /**
     * Determine whether the user can create a role.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create role');
    }

    /**
     * Determine whether the user can update a role.
     */
    public function update(User $user, Rol $rol): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update role');
    }

    /**
     * Determine whether the user can delete a role.
     */
    public function delete(User $user, Rol $rol): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete role');
    }
}

