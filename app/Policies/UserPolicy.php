<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver panel de usuarios');
    }

    public function view(User $user, User $targetUser): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('ver cualquier usuario');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('crear usuario');
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('actualizar usuario');
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasRole('ADMIN') ||
            $user->hasPermissionTo('eliminar usuario');
    }
}
