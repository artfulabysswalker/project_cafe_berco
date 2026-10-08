<?php

namespace App\Http\Middleware;

use App\Services\GuestSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestoreGuestMiddleware
{
    public function __construct(protected GuestSessionService $guests)
    {
        //
    }

    public function handle(Request $request, Closure $next)
    {
        // Jangan auto-login sebagai guest pada route auth, admin, dashboard,
        // settings, profile, fortify, dan API.
        if ($request->is(
            'login', 'register', 'staff-login', 'logout',
            'admin*', 'control*', 'dashboard*', 'settings*',
            'profile*', 'user*', 'api*',
            'forgot-password*', 'reset-password*', 'verify-email*', 'two-factor*', 'confirm-password*'
        )) {
            return $next($request);
        }

        if (! Auth::check()) {
            // Satu user guest per sesi, bukan satu user guest global. Ini yang
            // mencegah keranjang antar meja saling tertimpa.
            $guest = $this->guests->resolve($request);

            Auth::login($guest);
            $request->session()->put('guest_name', 'Guest');
        } elseif (! Auth::user()->is_guest) {
            // Pelanggan baru saja login sebagai akun sungguhan. Putuskan
            // tautan guest supaya setelah logout dia tidak diam-diam memakai
            // keranjang guest lama.
            $this->guests->detach($request);
        }

        return $next($request);
    }
}
