<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))
                ->with('error', 'Sesi login Anda telah berakhir. Silakan login kembali.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Hanya Administrator yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}