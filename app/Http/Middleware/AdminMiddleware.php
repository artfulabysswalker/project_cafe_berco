<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        // Check if user is Admin or Staff/Cashier
        if ($user && ($user->isAdmin() || $user->isStaff())) {
            return $next($request);
        }

        return redirect('/home');
    }
}
