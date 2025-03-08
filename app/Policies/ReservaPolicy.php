<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservaPolicy
{
    use HandlesAuthorization;

    
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel reservas');
    }

    public function view(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver cualquier reserva');
    }

   
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear reserva');
    }

  
    public function update(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar reserva');
    }

   
    public function delete(User $user, Reserva $reserva)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar reserva');
    }


   
}