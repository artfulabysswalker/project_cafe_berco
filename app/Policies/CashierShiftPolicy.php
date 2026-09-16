<?php

namespace App\Policies;

use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashierShiftPolicy
{
    use HandlesAuthorization;

    /**
     * View any shifts
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * View single shift: Admin can view all, Cashier can only view their own shift session.
     */
    public function view(User $user, CashierShift $shift): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $shift->user_id === $user->id_user;
    }

    /**
     * Open shift: Cashier can open a shift only if they do not currently have an open shift.
     */
    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return ! $user->activeShift()->exists();
    }

    /**
     * Close shift: Cashier can only close their own active shift. Admin can close any shift.
     */
    public function update(User $user, CashierShift $shift): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $shift->user_id === $user->id_user && $shift->status === 'open';
    }

    /**
     * Delete shift: Admin only.
     */
    public function delete(User $user, CashierShift $shift): bool
    {
        return $user->isAdmin();
    }
}
