<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any Rols.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN'); // Solo los administradores pueden ver Rols
    }

    /**
     * Determine if the user can view the Rol.
     */
    public function view(User $user, Rol $Rol)
    {
        return $user->hasRole('ADMIN'); // Solo los administradores pueden ver Rols individuales
    }

    /**
     * Determine if the user can create Rols.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN'); // Solo los administradores pueden crear Rols
    }

    /**
     * Determine if the user can update the Rol.
     */
    public function update(User $user, Rol $Rol)
    {
        return $user->hasRole('ADMIN'); // Solo los administradores pueden actualizar Rols
    }

    /**
     * Determine if the user can delete the Rol.
     */
    public function delete(User $user, Rol $Rol)
    {
        return $user->hasRole('ADMIN'); // Solo los administradores pueden eliminar Rols
    }
}
