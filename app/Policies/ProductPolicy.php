<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel inventario');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier inventario');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear inventario');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar inventario');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar inventario');
    }
}
