<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Producto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view product panel');
    }

    /**
     * Determine if the user can view a specific product.
     */
    public function view(User $user, Producto $producto): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view product');
    }

    /**
     * Determine if the user can create a product.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create product');
    }

    /**
     * Determine if the user can update a product.
     */
    public function update(User $user, Producto $producto): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update product');
    }

    /**
     * Determine if the user can delete a product.
     */
    public function delete(User $user, Producto $producto): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete product');
    }
}

