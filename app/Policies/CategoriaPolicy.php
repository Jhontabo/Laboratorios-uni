<?php

// app/Policies/CategoriaPolicy.php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier modelo.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determina si el usuario puede ver el modelo.
     */
    public function view(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determina si el usuario puede crear modelos.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determina si el usuario puede actualizar el modelo.
     */
    public function update(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determina si el usuario puede eliminar el modelo.
     */
    public function delete(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determina si el usuario puede restaurar el modelo.
     */
    public function restore(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente el modelo.
     */
    public function forceDelete(User $user, Categoria $categoria): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }
}
