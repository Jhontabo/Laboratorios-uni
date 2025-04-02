<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier usuario.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver panel usuarios');
    }

    /**
     * Determina si el usuario puede crear un usuario.laravel-app
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN'); // Asegurar que coincida con el nombre en la base de datos
    }

    /**
     * Determina si el usuario puede actualizar un usuario.
     */
    public function update(User $user, User $targetUser)
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determina si el usuario puede eliminar un usuario.
     */
    public function delete(User $user, User $targetUser)
    {
        return $user->hasRole('ADMIN');
    }
}
