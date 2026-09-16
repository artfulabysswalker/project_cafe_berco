<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * List all staff and users with role filtering
     */
    public function index()
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser?->isAdmin() ?? false;

        $admins = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Admin', 'admin', 'Owner', 'owner']);
        })->latest('id_user')->get();

        $staffs = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Staff', 'staff', 'Cashier', 'cashier', 'kasir', 'pegawai']);
        })->latest('id_user')->get();

        $users = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Customer', 'customer', 'pelanggan']);
        })->latest('id_user')->get();

        // Ensure Admin, Staff, Cashier, Customer exist
        $roles = Role::whereIn('role_name', ['Admin', 'Staff', 'Cashier', 'Customer'])->get();
        if ($roles->isEmpty()) {
            $roles = Role::all();
        }

        return view('admin.staffoption.index', compact(
            'admins',
            'staffs',
            'users',
            'roles',
            'isAdmin',
            'currentUser'
        ));
    }

    /**
     * Show create staff form
     */
    public function create()
    {
        $roles = Role::whereIn('role_name', ['Admin', 'Staff', 'Cashier'])->get();
        if ($roles->isEmpty()) {
            $roles = Role::all();
        }
        return view('admin.staffoption.create', compact('roles'));
    }

    /**
     * Store new staff account
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'id_role' => 'required|exists:roles,id_role',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'nullable|in:active,inactive',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Auto resolve email if not explicitly provided
        $email = $request->email;
        if (!$email) {
            if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                $email = $request->username;
            } else {
                $cleanUser = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->username));
                $email = $cleanUser . '@bercocafe.com';

                if (User::where('email', $email)->exists()) {
                    $email = $cleanUser . '_' . rand(100, 999) . '@bercocafe.com';
                }
            }
        }

        $user = User::create([
            'name' => trim($request->name),
            'username' => trim($request->username),
            'email' => $email,
            'id_role' => $request->id_role,
            'password' => Hash::make($request->password),
            'status' => $request->status ?: 'active',
        ]);

        return redirect()->route('admin.staffoption.index')
            ->with('success', 'Akun pegawai "' . $user->name . '" (' . ($user->role?->role_name ?? 'Staff') . ') berhasil dibuat!');
    }

    /**
     * Show edit form
     */
    public function edit($id_user)
    {
        $staff = User::where('id_user', $id_user)->orWhere('id', $id_user)->firstOrFail();
        $roles = Role::whereIn('role_name', ['Admin', 'Staff', 'Cashier', 'Customer'])->get();
        if ($roles->isEmpty()) {
            $roles = Role::all();
        }
        return view('admin.staffoption.edit', compact('staff', 'roles'));
    }

    /**
     * Update staff account
     */
    public function update(Request $request, $id_user)
    {
        $staff = User::where('id_user', $id_user)->orWhere('id', $id_user)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($staff->id_user, 'id_user')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id_user, 'id_user')],
            'id_role' => 'required|exists:roles,id_role',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|in:active,inactive',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Security check: prevent demoting primary Admin if only one admin exists
        $newRole = Role::find($request->id_role);
        if ($staff->isAdmin() && $newRole && !in_array($newRole->role_name, ['Admin', 'admin'])) {
            $adminCount = User::whereHas('role', fn($q) => $q->whereIn('role_name', ['Admin', 'admin']))->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat mengubah role satu-satunya Administrator utama.');
            }
        }

        $staff->name = trim($request->name);
        $staff->username = trim($request->username);
        if ($request->filled('email')) {
            $staff->email = trim($request->email);
        }
        $staff->id_role = $request->id_role;

        if ($request->filled('status')) {
            $staff->status = $request->status;
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        return redirect()->route('admin.staffoption.index')
            ->with('success', 'Data pegawai "' . $staff->name . '" berhasil diperbarui!');
    }

    /**
     * Quick Toggle active/inactive status
     */
    public function toggleStatus($id_user)
    {
        $staff = User::where('id_user', $id_user)->orWhere('id', $id_user)->firstOrFail();

        // Prevent disabling yourself or primary admin
        if ($staff->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.');
        }

        $staff->status = ($staff->status === 'inactive') ? 'active' : 'inactive';
        $staff->save();

        $statusLabel = $staff->status === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.staffoption.index')
            ->with('success', 'Status akun ' . $staff->name . ' berhasil ' . $statusLabel . '.');
    }

    /**
     * Reset password to a default or new password
     */
    public function resetPassword(Request $request, $id_user)
    {
        $staff = User::where('id_user', $id_user)->orWhere('id', $id_user)->firstOrFail();

        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $staff->password = Hash::make($request->new_password);
        $staff->save();

        return redirect()->route('admin.staffoption.index')
            ->with('success', 'Password untuk ' . $staff->name . ' berhasil direset!');
    }

    /**
     * Update Role
     */
    public function updateRole(Request $request, $id_user)
    {
        $request->validate([
            'id_role' => 'required|exists:roles,id_role'
        ]);

        $user = User::where('id_user', $id_user)->orWhere('id', $id_user)->firstOrFail();

        // Safety check
        $newRole = Role::find($request->id_role);
        if ($user->isAdmin() && $newRole && !in_array($newRole->role_name, ['Admin', 'admin'])) {
            $adminCount = User::whereHas('role', fn($q) => $q->whereIn('role_name', ['Admin', 'admin']))->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat mengubah role satu-satunya Administrator utama.');
            }
        }

        $user->id_role = $request->id_role;
        $user->save();

        return back()->with('success', 'Role pengguna ' . $user->name . ' berhasil diubah menjadi ' . ($newRole?->role_name ?? 'Baru') . '.');
    }

    /**
     * Delete staff
     */
    public function destroy($id)
    {
        $staff = User::where('id_user', $id)->orWhere('id', $id)->firstOrFail();

        // Prevent self deletion
        if ($staff->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deleting sole admin
        if ($staff->isAdmin()) {
            $adminCount = User::whereHas('role', fn($q) => $q->whereIn('role_name', ['Admin', 'admin']))->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus satu-satunya akun Administrator utama.');
            }
        }

        $name = $staff->name;
        $staff->delete();

        return redirect()->route('admin.staffoption.index')
            ->with('success', 'Akun "' . $name . '" berhasil dihapus.');
    }
}