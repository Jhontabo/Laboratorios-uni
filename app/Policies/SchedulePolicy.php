<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Horario;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any schedules.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view schedule panel');
    }

    /**
     * Determine whether the user can view a specific schedule.
     */
    public function view(User $user, Horario $horario): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view schedule');
    }

    /**
     * Determine whether the user can create a schedule.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create schedule');
    }

    /**
     * Determine whether the user can update a schedule.
     */
    public function update(User $user, Horario $horario): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update schedule');
    }

    /**
     * Determine whether the user can delete a schedule.
     */
    public function delete(User $user, Horario $horario): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete schedule');
    }
}

