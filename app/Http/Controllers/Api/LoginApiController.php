<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $role = $user->role ? $user->role->role_name : 'No Role';

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id_user' => $user->id_user,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $role,
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid email or password',
        ], 401);
    }
}
