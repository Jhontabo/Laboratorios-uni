<?php

namespace App\Policies;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier rol.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver panel roles');
    }

    /**
     * Determina si el usuario puede crear un rol.
     */
    public function create(User $user)
    {
        // Verifica si el usuario tiene el rol de 'ADMIN' o el permiso correspondiente
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear rol');
    }

    /**
     * Determina si el usuario puede actualizar un rol.
     */
    public function update(User $user, Rol $rol)
    {
        // Verifica si el usuario tiene el rol de 'ADMIN' o el permiso correspondiente
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar rol');
    }

    /**
     * Determina si el usuario puede eliminar un rol.
     */
    public function delete(User $user, Rol $rol)
    {
        // Verifica si el usuario tiene el rol de 'ADMIN' o el permiso correspondiente
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar rol');
    }
}
