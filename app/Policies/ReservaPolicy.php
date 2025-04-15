<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservaPolicy
{
    use HandlesAuthorization;

    // Métodos para gestión general de reservas
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel reservas');
    }

    public function view(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier reserva') ||
            $reserva->user_id === $user->id; // Dueño puede ver su reserva
    }

    public function create(User $user)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear reserva');
    }

    public function update(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar reserva') ||
            ($reserva->user_id === $user->id && $reserva->estado === 'pendiente');
    }

    public function delete(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar reserva') ||
            ($reserva->user_id === $user->id && $reserva->estado === 'pendiente');
    }

    // Métodos específicos para solicitudes
    public function viewSolicitudes(User $user)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel solicitudes reservas');
    }

    public function manageSolicitud(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('gestionar solicitudes reservas');
    }

    // Métodos para historial
    public function viewHistorial(User $user)
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver historial reservas');
    }
}
