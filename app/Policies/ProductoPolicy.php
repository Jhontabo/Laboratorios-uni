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
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel productos');
    }

    /**
     * Determina si el usuario puede ver un producto específico.
     */
    public function view(User $user, Producto $producto)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver producto');
    }

    /**
     * Determina si el usuario puede crear un producto.
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear producto');
    }

    /**
     * Determina si el usuario puede actualizar un producto.
     */
    public function update(User $user, Producto $producto)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar producto');
    }

    /**
     * Determina si el usuario puede eliminar un producto.
     */
    public function delete(User $user, Producto $producto)
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar producto');
    }
}