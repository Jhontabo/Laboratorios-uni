<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Producto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier producto.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver cualquier producto');
    }

    /**
     * Determina si el usuario puede ver un producto específico.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Producto  $producto
     * @return bool
     */
    public function view(User $user, Producto $producto)
    {
        return $user->hasPermissionTo('ver producto');
    }

    /**
     * Determina si el usuario puede crear un producto.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('crear producto');
    }

    /**
     * Determina si el usuario puede actualizar un producto.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Producto  $producto
     * @return bool
     */
    public function update(User $user, Producto $producto)
    {
        return $user->hasPermissionTo('actualizar producto');
    }

    /**
     * Determina si el usuario puede eliminar un producto.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Producto  $producto
     * @return bool
     */
    public function delete(User $user, Producto $producto)
    {
        return $user->hasPermissionTo('eliminar producto');
    }
}
