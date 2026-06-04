<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{


    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if (Auth::attempt($validated)) {

            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user has Admin or Staff role
            if ($user->isAdmin() || $user->isStaff()) {
               return redirect()->route('control.dashboard');
            }

            Auth::logout();
            return back()->with('error', 'Hanya Admin dan Staff yang dapat login di sini');
        }

        return back()->with('error', 'Username atau password salah')->withInput($request->only('username'));
    }
}