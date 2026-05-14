<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{


    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role && in_array($user->role->role_name, ['Admin', 'Staff'])) {
               return redirect()->route('control.dashboard');
            }

            Auth::logout();
            return back()->with('error', 'Unauthorized access');
        }

        return back()->with('error', 'Invalid login');
    }
}