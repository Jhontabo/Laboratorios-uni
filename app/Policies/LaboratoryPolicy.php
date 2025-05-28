<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Laboratory;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel de laboratorios');
    }

    public function view(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier laboratorio');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear laboratorio');
    }

    public function update(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar laboratorio');
    }

    public function delete(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar laboratorio');
    }
}
