<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetRequestController extends Controller
{
    // Show request form
    public function create()
    {
        return view('admin.reset.request');
    }


    // Submit reset request
    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'full_name' => 'required',
            'email' => 'required|email',
            'reason' => 'required'
        ]);

        PasswordResetRequest::create([
            'id_user' => $request->id_user,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()
            ->back()
            ->with('success','Request submitted.');
    }


    // Admin sees requests
    public function index()
    {
        $requests = PasswordResetRequest::where(
            'status',
            'pending'
        )->get();

        return view(
            'admin.requests.index',
            compact('requests')
        );
    }


    // Admin resets password
    public function resetDefault($id_user)
    {
        $user = User::findOrFail($id_user);

        $user->password = Hash::make('password123');

        $user->save();


        PasswordResetRequest::where(
            'id_user',
            $id_user
        )->update([
            'status' => 'resolved'
        ]);


        return back()->with(
            'success',
            'Password reset completed.'
        );
    }
}