<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Categoria;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

   /**
     * Determina si el usuario puede ver cualquier categoría.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        // Permite al usuario ver el panel de categorías si tiene el permiso adecuado
        return $user->hasPermissionTo('ver panel categorias');
    }


    
}
