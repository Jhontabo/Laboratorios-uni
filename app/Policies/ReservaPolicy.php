<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier reserva.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel reservas');
    }

    /**
     * Determina si el usuario puede ver una reserva específica.
     */
    public function view(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver cualquier reserva');
    }

    /**
     * Determina si el usuario puede crear una reserva.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear reserva');
    }

    /**
     * Determina si el usuario puede actualizar una reserva.
     */
    public function update(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar reserva');
    }

    /**
     * Determina si el usuario puede eliminar una reserva.
     */
    public function delete(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar reserva');
    }


    public static function canViewAny(User $user): bool
    {
        return $user->can('ver panel solicitudes reservas');
    }
}