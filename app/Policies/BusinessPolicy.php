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
}
