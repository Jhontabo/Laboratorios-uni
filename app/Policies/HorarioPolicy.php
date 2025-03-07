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
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver cualquier horario');
    }

    /**
     * Determina si el usuario puede ver un horario específico.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Horario  $horario
     * @return bool
     */
    public function view(User $user, Horario $horario)
    {
        return $user->hasPermissionTo('ver horario');
    }

    /**
     * Determina si el usuario puede crear un horario.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('crear horario');
    }

    /**
     * Determina si el usuario puede actualizar un horario.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Horario  $horario
     * @return bool
     */
    public function update(User $user, Horario $horario)
    {
        return $user->hasPermissionTo('actualizar horario');
    }

    /**
     * Determina si el usuario puede eliminar un horario.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Horario  $horario
     * @return bool
     */
    public function delete(User $user, Horario $horario)
    {
        return $user->hasPermissionTo('eliminar horario');
    }
}
