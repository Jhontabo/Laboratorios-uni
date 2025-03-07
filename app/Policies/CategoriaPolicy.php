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
        return $user->hasPermissionTo('ver cualquier categoria');
    }

    /**
     * Determina si el usuario puede ver una categoría específica.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Categoria  $categoria
     * @return bool
     */
    public function view(User $user, Categoria $categoria)
    {
        return $user->hasPermissionTo('ver categoria');
    }

    /**
     * Determina si el usuario puede crear una categoría.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('crear categoria');
    }

    /**
     * Determina si el usuario puede actualizar una categoría.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Categoria  $categoria
     * @return bool
     */
    public function update(User $user, Categoria $categoria)
    {
        return $user->hasPermissionTo('actualizar categoria');
    }

    /**
     * Determina si el usuario puede eliminar una categoría.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Categoria  $categoria
     * @return bool
     */
    public function delete(User $user, Categoria $categoria)
    {
        return $user->hasPermissionTo('eliminar categoria');
    }
}
