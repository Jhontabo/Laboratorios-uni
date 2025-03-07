<?php

// app/Policies/LaboratorioPolicy.php

namespace App\Policies;

use App\Models\Laboratorio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratorioPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('ver cualquier laboratorio');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('crear laboratorio');
    }

    public function update(User $user, Laboratorio $laboratorio)
    {
        return $user->hasPermissionTo('actualizar laboratorio');
    }

    public function delete(User $user, Laboratorio $laboratorio)
    {
        return $user->hasPermissionTo('eliminar laboratorio');
    }
}
