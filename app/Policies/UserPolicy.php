<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->name === 'Jhon Tajumbina';
    }

    public function view(User $user, User $model): bool
    {
        return $user->name === 'Jhon Tajumbina';
    }

    public function create(User $user): bool
    {
        return $user->name === 'Jhon Tajumbina';
    }

    public function update(User $user, User $model): bool
    {
        return $user->name === 'Jhon Tajumbina';
    }

    public function delete(User $user, User $model): bool
    {
        return $user->name === 'Jhon Tajumbina';
    }
}
