<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel solicitud reservas');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier solicitud reserva');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear solicitud reserva');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar solicitud reserva');
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar solicitud reserva');
    }
}
