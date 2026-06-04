<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function loginForm(Request $request)
    {
        $token = csrf_token();
        return view('auth.login', ['csrf_token' => $token]);
    }

    public function testPost(Request $request)
    {
        return response()->json([
            'message' => 'POST successful',
            'csrf_ok' => true,
            'session_id' => session()->getId(),
        ]);
    }
}
