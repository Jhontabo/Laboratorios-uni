<?php

namespace App\Policies;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratorioPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier laboratorio.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel laboratorios');
    }

    /**
     * Determina si el usuario puede ver un laboratorio específico.
     */
    public function view(User $user, Laboratorio $laboratorio)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver laboratorio');
    }

    /**
     * Determina si el usuario puede crear un laboratorio.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear laboratorio');
    }

    /**
     * Determina si el usuario puede actualizar un laboratorio.
     */
    public function update(User $user, Laboratorio $laboratorio)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar laboratorio');
    }

    /**
     * Determina si el usuario puede eliminar un laboratorio.
     */
    public function delete(User $user, Laboratorio $laboratorio)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar laboratorio');
    }
}