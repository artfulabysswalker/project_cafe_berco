<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        // Check if user is Admin or Staff (based on role_name)
        if ($user->role && in_array($user->role->role_name, ['Admin', 'Staff'])) {
            return $next($request);
        }

        return redirect('/home');
    }
}