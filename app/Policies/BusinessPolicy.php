<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    /*
    |--------------------------------------------------------------------------
    | View Policies
    |--------------------------------------------------------------------------
    */

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Business $business): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Mutation Policies
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Business $business): bool
    {
        return $business->canBeManagedBy($user);
    }

    public function delete(User $user, Business $business): bool
    {
        return $business->canBeManagedBy($user);
    }

    public function restore(User $user, Business $business): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Business $business): bool
    {
        return $user->role === 'admin';
    }
}
