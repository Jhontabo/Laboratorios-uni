<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel de reservas');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('ver cualquier reserva')
            || $booking->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear reserva');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('actualizar reserva')
            || ($booking->user_id === $user->id && $booking->estado === 'pendiente');
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('eliminar reserva')
            || ($booking->user_id === $user->id && $booking->estado === 'pendiente');
    }

    // Panel de solicitudes de reserva
    public function viewRequests(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel de solicitudes de reserva');
    }

    // Historial de reservas
    public function viewHistory(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel de historial de reservas');
    }
}
