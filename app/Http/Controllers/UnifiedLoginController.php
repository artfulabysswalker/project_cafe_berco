<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;

class UnifiedLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && ! Auth::user()->is_guest) {
            if (Auth::user()->isAdmin() || Auth::user()->isStaff()) {
                return redirect()->route('control.dashboard');
            }

            return redirect()->route('menu.index');
        }

        return view('auth.login');
    }

    /**
     * Handle login for both customer and admin/staff (supports Email or Username)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email atau Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        $input = trim($request->email);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $primaryField = $isEmail ? 'email' : 'username';
        $fallbackField = $isEmail ? 'username' : 'email';

        // 1. Try with primary field
        $authenticated = Auth::attempt([$primaryField => $input, 'password' => $request->password], $request->boolean('remember'));

        // 2. Try with fallback field if primary failed
        if (! $authenticated) {
            $authenticated = Auth::attempt([$fallbackField => $input, 'password' => $request->password], $request->boolean('remember'));
        }

        if ($authenticated) {
            $user = Auth::user();

            // Check active status
            if (! $user->isActive()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.',
                ]);
            }

            // Check if Two-Factor Authentication is enabled
            if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
                Auth::logout();
                $request->session()->put([
                    'login.id' => $user->getKey(),
                    'login.remember' => $request->boolean('remember'),
                ]);

                return redirect()->route('two-factor.login');
            }

            $request->session()->regenerate();

            // Check if admin or staff
            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->intended(route('dashboard', absolute: false))->with('success', 'Selamat datang, '.$user->name.'!');
            }

            // Regular customer
            return redirect()->intended(route('dashboard', absolute: false))->with('success', 'Login berhasil!');
        }

        throw ValidationException::withMessages([
            'email' => 'Email/Username atau password salah.',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
