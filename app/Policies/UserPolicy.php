<?php

// app/Policies/UserPolicy.php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver cualquier usuario');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('crear usuario');
    }

    public function update(User $user)
    {
        return $user->hasPermissionTo('actualizar usuario');
    }

    public function delete(User $user)
    {
        return $user->hasPermissionTo('eliminar usuario');
    }
}
