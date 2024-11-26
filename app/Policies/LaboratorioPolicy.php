<?php

namespace App\Policies;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratorioPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Laboratorista');
    }
}
