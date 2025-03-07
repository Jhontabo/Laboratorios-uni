<?php

namespace App\Policies;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratorioPolicy
{
    use HandlesAuthorization;

   
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel laboratorios');
    }

  
    public function view(User $user, Laboratorio $laboratorio)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver laboratorio');
    }

    
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear laboratorio');
    }

    
    public function update(User $user, Laboratorio $laboratorio)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar laboratorio');
    }

   

    public function delete(User $user, Laboratorio $laboratorio)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar laboratorio');
    }
}