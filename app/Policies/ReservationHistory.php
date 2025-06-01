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
            $user->hasPermissionTo('ver historial de reservas');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier historial de reservas');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear historial de reservas');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar historial de reservas');
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar historial de reservas');
    }
}
