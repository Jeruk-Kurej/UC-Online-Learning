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
}
