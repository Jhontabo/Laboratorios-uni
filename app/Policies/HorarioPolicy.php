<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Horario;
use Illuminate\Auth\Access\HandlesAuthorization;

class HorarioPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier horario.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel horarios');
    }

    /**
     * Determina si el usuario puede ver un horario específico.
     */
    public function view(User $user, Horario $horario)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver horario');
    }

    /**
     * Determina si el usuario puede crear un horario.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear horario');
    }

    /**
     * Determina si el usuario puede actualizar un horario.
     */
    public function update(User $user, Horario $horario)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar horario');
    }

    /**
     * Determina si el usuario puede eliminar un horario.
     */
    public function delete(User $user, Horario $horario)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar horario');
    }
}