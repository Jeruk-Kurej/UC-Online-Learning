<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    // --- Current user ---

    protected function currentUserIsAdmin(): bool
    {
        return (bool) request()->user()?->isAdmin();
    }

    /**
     * Where Cancel on a business or work profile form leads: the admin list for admins, "my businesses" for everyone else.
     */
    protected function venturesHomeUrl(): string
    {
        return $this->currentUserIsAdmin() ? route('businesses.admin') : route('businesses.my');
    }
}
