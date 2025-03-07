<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SolicitudesReservasPolicy
{
    use HandlesAuthorization;
    

    /**
     * Determina si el usuario puede ver cualquier solicitud de reserva.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel solicitudes reservas');
    }

    /**
     * Determina si el usuario puede ver una solicitud específica.
     */
    public function view(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver solicitud reserva');
    }

    /**
     * Determina si el usuario puede crear una solicitud de reserva.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear solicitud reserva');
    }

    /**
     * Determina si el usuario puede actualizar una solicitud de reserva.
     */
    public function update(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar solicitud reserva');
    }

    /**
     * Determina si el usuario puede eliminar una solicitud de reserva.
     */
    public function delete(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar solicitud reserva');
    }

    public static function canViewAny(User $user): bool
    {
        return $user->can('ver panel solicitudes reservas');
    }
}