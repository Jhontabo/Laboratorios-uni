<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Laboratorio;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view laboratory panel');
    }

    public function view(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view laboratory');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create laboratory');
    }

    public function update(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update laboratory');
    }

    public function delete(User $user, Laboratorio $laboratorio): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete laboratory');
    }
}

