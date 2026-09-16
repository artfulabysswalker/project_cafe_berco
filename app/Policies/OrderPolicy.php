<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Semua pengguna terautentikasi (Admin & Kasir) dapat mengakses daftar index order mereka.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Owner/Admin dapat melihat detail transaksi manapun.
     * Kasir hanya dapat melihat transaksi yang diproses di akun/shift miliknya.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $order->id_user === $user->id_user;
    }

    /**
     * Kasir hanya dapat membuat transaksi baru jika memiliki shift yang berstatus 'open'.
     * Admin/Owner memiliki izin bypass untuk keperluan testing atau darurat.
     */
    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->activeShift()->exists();
    }

    /**
     * Update order (hanya admin atau kasir pemilik transaksi pada shift open).
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $order->id_user === $user->id_user && $user->activeShift()->exists();
    }

    /**
     * Delete order (hanya admin/owner).
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
