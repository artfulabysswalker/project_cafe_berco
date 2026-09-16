<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau Email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        $input = trim($request->username);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $primaryField = $isEmail ? 'email' : 'username';
        $fallbackField = $isEmail ? 'username' : 'email';

        $authenticated = Auth::attempt([$primaryField => $input, 'password' => $request->password]);
        if (! $authenticated) {
            $authenticated = Auth::attempt([$fallbackField => $input, 'password' => $request->password]);
        }

        if ($authenticated) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Check active status
            if (! $user->isActive()) {
                Auth::logout();

                return back()->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.');
            }

            // Check if user has Admin or Staff role
            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->route('control.dashboard')->with('success', 'Selamat datang, '.$user->name.'!');
            }

            Auth::logout();

            return back()->with('error', 'Hanya Admin dan Staff yang dapat login di sini');
        }

        return back()->with('error', 'Username/Email atau password salah')->withInput($request->only('username'));
    }
}
