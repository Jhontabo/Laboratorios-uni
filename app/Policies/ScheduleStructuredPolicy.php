<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ScheduleStructured;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScheduleStructuredPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel de horarios');
    }

    public function view(User $user, ScheduleStructured $ss): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver cualquier horario');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear horario');
    }

    public function update(User $user, ScheduleStructured $ss): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar horario');
    }

    public function delete(User $user, ScheduleStructured $ss): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar horario');
    }
}
