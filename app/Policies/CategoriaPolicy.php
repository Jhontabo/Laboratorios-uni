<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Categoria;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

    
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel categorias');
    }

  
    public function view(User $user, Categoria $categoria)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver categoria');
    }


    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear categoria');
    }

   
    public function update(User $user, Categoria $categoria)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar categoria');
    }

   
    public function delete(User $user, Categoria $categoria)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar categoria');
    }
}