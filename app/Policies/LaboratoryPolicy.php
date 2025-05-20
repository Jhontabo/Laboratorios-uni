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
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view laboratory panel');
    }

    public function view(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view laboratory');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create laboratory');
    }

    public function update(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update laboratory');
    }

    public function delete(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete laboratory');
    }
}

