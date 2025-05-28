<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Loan;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver panel de productos');
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('ver cualquier producto');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('crear producto');
    }

    public function update(User $user, Loan $loan): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('actualizar producto');
    }

    public function delete(User $user, Loan $loan): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('eliminar producto');
    }
}
