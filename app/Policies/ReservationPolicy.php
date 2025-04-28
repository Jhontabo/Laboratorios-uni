<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any reservations.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view reservation panel');
    }

    /**
     * Determine if the user can view a specific reservation.
     */
    public function view(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('view any reservation')
            || $reserva->user_id === $user->user_id;
    }

    /**
     * Determine if the user can create a reservation.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create reservation');
    }

    /**
     * Determine if the user can update a reservation.
     */
    public function update(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('update reservation')
            || ($reserva->user_id === $user->user_id && $reserva->estado === 'pendiente');
    }

    /**
     * Determine if the user can delete a reservation.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('delete reservation')
            || ($reserva->user_id === $user->user_id && $reserva->estado === 'pendiente');
    }

    /**
     * Determine if the user can view the reservation requests panel.
     */
    public function viewRequests(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view reservation requests panel');
    }

    /**
     * Determine if the user can manage a specific reservation request.
     */
    public function manageRequest(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('manage reservation requests');
    }

    /**
     * Determine if the user can view the reservation history.
     */
    public function viewHistory(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view reservation history');
    }
}

