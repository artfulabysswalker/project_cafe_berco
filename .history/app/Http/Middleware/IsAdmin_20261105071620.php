<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Check whether the user is logged in
        if (!Auth::check()) {
            return redirect()->route('staff.login.form');
        }

        $user = Auth::user();

        // Check Admin role
        if ($user->role && $user->role->role_name === 'Admin') {
            return $next($request);
        }

        // Deny access to other roles
        abort(403, 'Hanya Admin yang dapat mengakses resource ini.');
    }
    