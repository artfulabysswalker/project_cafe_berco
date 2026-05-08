<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referee_id',
        'referral_code',
        'reward_amount',
        'status',
        'first_order_id',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'reward_amount' => 'decimal:2',
    ];

    /**
     * Get the referrer (person who referred)
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the referee (person being referred)
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    /**
     * Get the first order from referee
     */
    public function firstOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'first_order_id');
    }

    /**
     * Generate unique referral code
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = 'REF' . Str::random(10);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Mark referral as completed
     */
    public function markAsCompleted(Order $order): void
    {
        $this->update([
            'status' => 'completed',
            'first_order_id' => $order->id,
            'completed_at' => now(),
        ]);

        // Add reward to referrer
        $this->referrer->increment('referral_balance', $this->reward_amount);
    }
}
