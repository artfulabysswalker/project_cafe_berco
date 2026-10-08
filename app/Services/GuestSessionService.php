<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Mengelola identitas guest untuk pelanggan yang belum punya akun.
 *
 * Setiap perangkat / sesi browser mendapat user guest sendiri supaya keranjang
 * dan riwayat pesanan tidak tercampur antar meja. Objek user yang sama dipakai
 * ulang selama sesi hidup, jadi pelanggan yang memindai ulang QR di meja yang
 * sama tidak kehilangan keranjangnya.
 */
class GuestSessionService
{
    /**
     * Kunci session tempat id_user guest disimpan.
     */
    public const SESSION_KEY = 'guest_user_id';

    /**
     * Ambil user guest milik sesi ini, buat baru bila belum ada / sudah hilang.
     */
    public function resolve(Request $request): User
    {
        $user = $this->current($request);

        if ($user) {
            return $user;
        }

        return $this->create($request);
    }

    /**
     * User guest yang tertaut ke sesi ini, atau null bila belum pernah dibuat.
     */
    public function current(Request $request): ?User
    {
        $id = $request->session()->get(self::SESSION_KEY);

        if (blank($id)) {
            return null;
        }

        $user = User::find($id);

        // User sudah dihapus (mis. oleh prune), atau bukan guest lagi karena
        // ia baru saja login sebagai akun sungguhan.
        if (! $user || ! $user->is_guest) {
            $request->session()->forget(self::SESSION_KEY);

            return null;
        }

        return $user;
    }

    /**
     * Buat user guest baru dan tautkan ke sesi ini.
     */
    public function create(Request $request): User
    {
        $role = Role::firstOrCreate(['role_name' => 'Guest']);

        // username dan email punya unique constraint, jadi butuh nilai unik.
        $token = Str::lower(Str::random(16));

        $user = User::create([
            'name' => 'Guest',
            'username' => 'guest-'.$token,
            'email' => 'guest-'.$token.'@berco.local',
            'password' => Hash::make(Str::random(32)),
            'id_role' => $role->id_role,
            'is_guest' => true,
        ]);

        $request->session()->put(self::SESSION_KEY, $user->id_user);
        $request->session()->put('is_guest', true);

        return $user;
    }

    /**
     * Lepas user guest dari sesi tanpa menghapusnya dari database.
     *
     * Dipanggil setelah pelanggan login sebagai akun sungguhan supaya sesi
     * berikutnya tidak diam-diam memakai akun itu lagi.
     */
    public function detach(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->forget('is_guest');
    }
}
