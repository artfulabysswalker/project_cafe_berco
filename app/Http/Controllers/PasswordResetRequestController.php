<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;

class PasswordResetRequestController extends Controller
{
    /**
     * Show list of password reset requests
     */
    public function index()
    {
        $requests = PasswordResetRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.password-reset-requests.index', compact('requests'));
    }

    /**
     * Show form for creating reset request
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Store password reset request
     */
    public function store(Request $request)
    {
        $usernameOrEmail = $request->input('username') ?? $request->input('email');

        $user = null;
        if ($usernameOrEmail) {
            $user = User::where('username', $usernameOrEmail)
                ->orWhere('email', $usernameOrEmail)
                ->first();
        } elseif (auth()->check()) {
            $user = auth()->user();
        }

        if ($user) {
            PasswordResetRequest::create([
                'id_user' => $user->id_user,
                'status' => 'pending',
            ]);
        }

        return back()->with('success', 'Permintaan reset password telah dikirim ke administrator.');
    }

    /**
     * Reset user password to default
     */
    public function resetDefault(Request $request, $id_user)
    {
        $passwordResetRequest = PasswordResetRequest::where('id_user', $id_user)->first();

        if (!$passwordResetRequest) {
            return back()->with('error', 'Request tidak ditemukan');
        }

        // Reset password to default (123456)
        $user = $passwordResetRequest->user;
        if ($user) {
            $user->update(['password' => bcrypt('123456')]);
        }

        // Mark request as processed
        $passwordResetRequest->update(['status' => 'processed']);

        return back()->with('success', 'Password user berhasil direset ke default (123456)');
    }
}
