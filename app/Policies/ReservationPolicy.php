<?php

namespace App\Policies;

use App\Models\Booking;
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
    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('view any reservation')
            || $booking->user_id === $user->user_id;
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
    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('update reservation')
            || ($booking->user_id === $user->user_id && $booking->estado === 'pendiente');
    }

    /**
     * Determine if the user can delete a reservation.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('delete reservation')
            || ($booking->user_id === $user->user_id && $booking->estado === 'pendiente');
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
    public function manageRequest(User $user, Booking $booking): bool
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

