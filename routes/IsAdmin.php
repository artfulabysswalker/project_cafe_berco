<?php

// Pastikan file ini berada di folder app/Http/Middleware/
// agar terbaca oleh rute Anda.
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Anda tidak memiliki akses admin.');
    }
}