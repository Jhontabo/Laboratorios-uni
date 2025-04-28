<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view category panel');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('view category');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('create category');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('update category');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('ADMIN') || $user->hasPermissionTo('delete category');
    }
}

