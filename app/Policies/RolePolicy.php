<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('Admin'); // Solo los administradores pueden ver roles
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(User $user, Role $role)
    {
        return $user->hasRole('Admin'); // Solo los administradores pueden ver roles individuales
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user)
    {
        return $user->hasRole('Admin'); // Solo los administradores pueden crear roles
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(User $user, Role $role)
    {
        return $user->hasRole('Admin'); // Solo los administradores pueden actualizar roles
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(User $user, Role $role)
    {
        return $user->hasRole('Admin'); // Solo los administradores pueden eliminar roles
    }
}
