<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EquipmentDecommission;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentDecommissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN');
    }

    public function view(User $user, EquipmentDecommission $decommission): bool
    {
        return $user->hasRole('ADMIN');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN');
    }

    public function update(User $user, EquipmentDecommission $decommission): bool
    {
        return $user->hasRole('ADMIN');
    }

    public function delete(User $user, EquipmentDecommission $decommission): bool
    {
        return $user->hasRole('ADMIN');
    }
}
