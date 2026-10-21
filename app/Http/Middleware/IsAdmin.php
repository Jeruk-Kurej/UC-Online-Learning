<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'))
                ->with('error', 'Your session has ended. Please log in again.');
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            abort(403, 'Only admins can open this page.');
        }

        return $next($request);
    }
}
