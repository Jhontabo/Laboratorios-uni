<?php
// app/Policies/HistorialReservasPolicy.php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HistorialReservasPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver panel historial reservas');
    }

    public function create(User $user)
    {
        return false; // El historial de reservas no se puede crear
    }

    public function update(User $user, Reserva $reserva)
    {
        return false; // El historial de reservas no se puede actualizar
    }

    public function delete(User $user, Reserva $reserva)
    {
        return false; // El historial de reservas no se puede eliminar
    }
}
