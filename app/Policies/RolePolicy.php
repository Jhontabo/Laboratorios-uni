<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role as RoleModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel roles');
    }

    public function view(User $user, RoleModel $role): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier rol');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear rol');
    }

    public function update(User $user, RoleModel $role): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar rol');
    }

    public function delete(User $user, RoleModel $role): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar rol');
    }
}
