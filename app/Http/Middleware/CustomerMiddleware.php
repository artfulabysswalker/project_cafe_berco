<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        // Customer role, guest, or any non-admin/non-staff user
        if ($user->isCustomer() || $user->isGuest() || strtolower($user->role?->role_name ?? '') === 'customer' || strtolower($user->role?->role_name ?? '') === 'guest' || (! $user->isAdmin() && ! $user->isStaff())) {
            return $next($request);
        }

        return redirect('/');
    }
}
