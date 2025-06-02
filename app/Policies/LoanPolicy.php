<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Loan;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * ¿Puede ver el listado de préstamos (panel o “mis préstamos”)?
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('ver panel mis prestamos');
    }

    /**
     * ¿Puede ver un préstamo específico?
     * - Admin y Coordinador: cualquiera.
     * - Resto: solo el propio.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('ver cualquier mis prestamos')
            || $loan->user_id === $user->id;
    }

    /**
     * ¿Puede crear un préstamo?
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('crear mis prestamos');
    }

    /**
     * ¿Puede actualizar un préstamo?
     * (Por ejemplo: cambiar estado, fechas, etc.)
     */
    public function update(User $user, Loan $loan): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('actualizar mis prestamos');
    }

    /**
     * ¿Puede eliminar un préstamo?
     */
    public function delete(User $user, Loan $loan): bool
    {
        return $user->hasRole('ADMIN')
            || $user->hasPermissionTo('eliminar mis prestamos');
    }
}
