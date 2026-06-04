<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        if ($user && $user->role && $user->role->role_name === 'Admin') {
            return $next($request);
        }

        abort(403, 'Hanya Admin yang dapat mengakses resource ini');
    }
}