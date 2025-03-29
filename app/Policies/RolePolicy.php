<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasRole('super_admin');
    }
}