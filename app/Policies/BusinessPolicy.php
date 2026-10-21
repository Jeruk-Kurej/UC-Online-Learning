<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    // --- Abilities ---

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
        return $user->isAdmin() || $business->user_id === $user->id;
    }
}
