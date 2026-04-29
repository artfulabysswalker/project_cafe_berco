<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\User;
use App\Models\Notification;

class VoucherService
{
    /**
     * Assign a voucher to a user
     */
    public function assignVoucherToUser(Voucher $voucher, User $user): bool
    {
        // Check if user already has this voucher
        if ($voucher->users()->where('users.id', $user->id)->exists()) {
            return false;
        }

        // Check if voucher has quantity available
        if (!$voucher->hasQuantityAvailable()) {
            return false;
        }

        // Assign voucher
        $voucher->assignToUser($user);

        // Increment quantity used
        $voucher->increment('quantity_used');

        // Create notification for user
        $this->createVoucherNotification($voucher, $user);

        return true;
    }

    /**
     * Create notification for voucher assignment
     */
    public function createVoucherNotification(Voucher $voucher, User $user): Notification
    {
        $discount = $voucher->discount_type === 'percentage' 
            ? $voucher->discount_value . '%' 
            : 'Rp' . number_format($voucher->discount_value, 0, ',', '.');

        $message = "Anda mendapatkan voucher promo: {$voucher->title} dengan diskon {$discount}. "
            . "Berlaku hingga " . $voucher->valid_until->format('d M Y');

        return Notification::create([
            'user_id' => $user->id,
            'title' => 'Voucher Promo Baru!',
            'message' => $message,
            'type' => 'voucher_offered',
            'channel' => 'both',
        ]);
    }

    /**
     * Send automatic vouchers to inactive customers
     * This method should be called from a scheduled job
     */
    public function sendVouchersToInactiveCustomers(): int
    {
        // Get automatic vouchers that are valid
        $automaticVouchers = Voucher::where('voucher_type', 'automatic')
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();

        if ($automaticVouchers->isEmpty()) {
            return 0;
        }

        // Get inactive users
        $inactiveUsers = User::whereNotNull('last_visit_at')
            ->where('notification_enabled', true)
            ->where('is_active', true)
            ->where('last_visit_at', '<=', now()->subDays(30))
            ->get();

        $totalSent = 0;

        foreach ($automaticVouchers as $voucher) {
            foreach ($inactiveUsers as $user) {
                if ($this->assignVoucherToUser($voucher, $user)) {
                    $totalSent++;
                }
            }
        }

        return $totalSent;
    }
}
