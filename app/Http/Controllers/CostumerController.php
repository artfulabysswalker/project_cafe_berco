<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CostumerController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        // Generate username from email (before @)
        $username = explode('@', $request->email)[0];
        // Ensure username is unique by adding suffix if needed
        $counter = 1;
        $originalUsername = $username;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        // Get customer role
        $customerRole = Role::where('role_name', 'customer')->first();

        // Create user
        User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $customerRole->id_role,
            'is_guest' => false,
        ]);

        Auth::login(User::where('email', $request->email)->first());

        return redirect()->route('menu.index')
            ->with('success', 'Account created successfully');
    }



    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Ensure user has proper role setup and is not guest
            if (!$user->id_role) {
                $customerRole = Role::where('role_name', 'customer')->first();
                $user->update(['id_role' => $customerRole->id_role]);
            }
            
            $user->update(['is_guest' => false]);
            $request->session()->regenerate();

            return redirect()->route('home')
                ->with('success', 'Login berhasil');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }
}