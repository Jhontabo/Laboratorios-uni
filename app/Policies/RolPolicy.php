<?php

// app/Policies/RolPolicy.php

namespace App\Policies;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver cualquier rol');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('crear rol');
    }

    public function update(User $user, Rol $rol)
    {
        return $user->hasPermissionTo('actualizar rol');
    }

    public function delete(User $user, Rol $rol)
    {
        return $user->hasPermissionTo('eliminar rol');
    }
}
