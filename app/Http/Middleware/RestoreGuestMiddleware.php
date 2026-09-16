<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RestoreGuestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Don't auto-login as guest on auth, admin, dashboard, settings, profile, fortify routes
        if ($request->is(
            'login', 'register', 'staff-login', 'logout',
            'admin*', 'control*', 'dashboard*', 'settings*',
            'profile*', 'user*', 'api*',
            'forgot-password*', 'reset-password*', 'verify-email*', 'two-factor*', 'confirm-password*'
        )) {
            return $next($request);
        }

        if (! Auth::check()) {
            $guest = User::where('is_guest', true)->first();

            if (! $guest) {
                $role = Role::firstOrCreate(['role_name' => 'Guest']);
                $guest = User::create([
                    'name' => 'Guest User',
                    'username' => 'guest',
                    'email' => 'guest@berco.local',
                    'password' => Hash::make('guest12345'),
                    'id_role' => $role->id_role ?? 2,
                    'is_guest' => true,
                ]);
            }

            Auth::login($guest);
            $request->session()->put('is_guest', true);
            $request->session()->put('guest_name', 'Guest');
        }

        return $next($request);
    }
}
