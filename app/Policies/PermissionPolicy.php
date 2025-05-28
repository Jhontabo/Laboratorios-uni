<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission as Perm;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel de permisos');
    }

    public function view(User $user, Perm $permission): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier permiso');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear permiso');
    }

    public function update(User $user, Perm $permission): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar permiso');
    }

    public function delete(User $user, Perm $permission): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar permiso');
    }
}
