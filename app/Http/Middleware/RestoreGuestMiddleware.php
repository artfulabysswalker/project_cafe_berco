<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestoreGuestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() && $request->session()->get('is_guest')) {
            $guest = User::where('is_guest', true)->first();

            if ($guest) {
                Auth::login($guest);
            }
        }

        return $next($request);
    }
}
