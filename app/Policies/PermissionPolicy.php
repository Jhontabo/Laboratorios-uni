<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permiso;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view permission panel');
    }

    /**
     * Determine if the user can view a specific permission.
     */
    public function view(User $user, Permiso $permiso): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view permission');
    }

    /**
     * Determine if the user can create a permission.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create permission');
    }

    /**
     * Determine if the user can update a permission.
     */
    public function update(User $user, Permiso $permiso): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update permission');
    }

    /**
     * Determine if the user can delete a permission.
     */
    public function delete(User $user, Permiso $permiso): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete permission');
    }
}

