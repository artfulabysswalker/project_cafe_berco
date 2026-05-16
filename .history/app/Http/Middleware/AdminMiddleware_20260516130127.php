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

        // admin or staff
        if ($user->id_role == 1 || $user->id_role == 3) {
            return $next($request);
        }

        return redirect('/home');
    }
}