<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // List all staff
    public function index()
    {
        $admins = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Admin');
        })->get();

        $staffs = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Staff');
        })->get();

        $users = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Customer');
        })->get();

        $roles = Role::all();

        return view('admin.staffoption.index', compact(
            'admins',
            'staffs',
            'users',
            'roles'
        ));
    }

    // Show create staff form
    public function create()
    {
        $roles = Role::all();

        return view('admin.staffoption.create', compact('roles'));
    }

    // Store new staff
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'id_role' => 'required|exists:roles,id_role',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'id_role' => $request->id_role,
            'password' => Hash::make($request->password),
            'status' => 'active', // ✅ important fix (prevents null issues)
        ]);

        return redirect()
            ->route('admin.staffoption.index')
            ->with('success', 'Staff account created successfully.');
    }

    // Show edit form
    public function edit($id_user)
    {
        $staff = User::findOrFail($id_user);
        $roles = Role::all();

        return view('admin.staffoption.edit', compact('staff', 'roles'));
    }

    // Update staff
    public function update(Request $request, $id_user)
    {
        $staff = User::findOrFail($id_user);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $staff->id_user . ',id_user',
            'id_role' => 'required|exists:roles,id_role',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $staff->name = $request->name;
        $staff->username = $request->username;
        $staff->id_role = $request->id_role;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        return redirect()
            ->route('admin.staffoption.index')
            ->with('success', 'Staff account updated successfully.');
    }

    // Toggle active/inactive status
    public function toggleStatus($id_user)
    {
        $staff = User::findOrFail($id_user);

        // safer toggle logic
        $staff->status = ($staff->status === 'active') ? 'inactive' : 'active';

        $staff->save();

        return redirect()
            ->route('admin.staffoption.index')
            ->with('success', 'Staff status updated successfully.');
    }

    // Update role only
    public function updateRole(Request $request, $id_user)
    {
        $request->validate([
            'id_role' => 'required|exists:roles,id_role'
        ]);

        $user = User::findOrFail($id_user);
        $user->id_role = $request->id_role;
        $user->save();

        return back()->with('success', 'Role updated');
    }

    // Delete staff
    public function destroy($id_user)
    {
        $staff = User::findOrFail($id_user);
        $staff->delete();

        return redirect()
            ->route('admin.staffoption.index')
            ->with('success', 'Staff account deleted successfully.');
    }
}