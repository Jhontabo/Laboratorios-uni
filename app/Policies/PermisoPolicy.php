<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermisoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier permiso.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel permisos');
    }

    /**
     * Determina si el usuario puede ver un permiso específico.
     */
    public function view(User $user, Permiso $permiso)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver permisos');
    }

    /**
     * Determina si el usuario puede crear un permiso.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear permisos');
    }

    /**
     * Determina si el usuario puede actualizar un permiso.
     */
    public function update(User $user, Permiso $permiso)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar permisos');
    }

    /**
     * Determina si el usuario puede eliminar un permiso.
     */
    public function delete(User $user, Permiso $permiso)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar permisos');
    }
}