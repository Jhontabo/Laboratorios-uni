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
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel horarios'); // 🔹 Cambia el permiso aquí
    }
    public function create(User $user)
    {
        return false; 
    }

    public function update(User $user, Reserva $reserva)
    {
        return false; 
    }

    public function delete(User $user, Reserva $reserva)
    {
        return false; 
    }
}
