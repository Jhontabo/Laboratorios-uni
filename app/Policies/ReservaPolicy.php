<?php

// app/Policies/ReservaPolicy.php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver cualquier reserva');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('crear reserva');
    }

    public function update(User $user, Reserva $reserva)
    {
        return $user->hasPermissionTo('actualizar reserva');
    }

    public function delete(User $user, Reserva $reserva)
    {
        return $user->hasPermissionTo('eliminar reserva');
    }
}
