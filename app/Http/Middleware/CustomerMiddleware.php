<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        // customer role (id_role 4) or guest customer
        if (($user->id_role == 4 && !$user->is_guest) || $user->is_guest) {
            return $next($request);
        }

        return redirect('/');
    }
}