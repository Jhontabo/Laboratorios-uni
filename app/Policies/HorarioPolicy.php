<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Horario;
use Illuminate\Auth\Access\HandlesAuthorization;

class HorarioPolicy
{
    use HandlesAuthorization;

    
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel horarios'); 
    }
  

    public function view(User $user, Horario $horario)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver horario');
    }

    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear horario');
    }

    
    public function update(User $user, Horario $horario)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar horario');
    }

   
    public function delete(User $user, Horario $horario)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar horario');
    }
}