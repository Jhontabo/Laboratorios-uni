<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AvailableProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvailableProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Cambié el nombre del permiso a "ver panel de productos disponibles"
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel solicitudes prestamos');
    }

    public function view(User $user, AvailableProduct $availableProduct): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver cualquier solicitud prestamo');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear solicitud prestamo');
    }

    public function update(User $user, AvailableProduct $availableProduct): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar solicitud prestamo');
    }

    public function delete(User $user, AvailableProduct $availableProduct): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar solicitud prestamo');
    }
}
