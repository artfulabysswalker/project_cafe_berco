<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    /** @use HasFactory<\Database\Factories\VoucherFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_amount',
        'discount_percentage',
        'max_discount',
        'min_purchase',
        'max_uses',
        'used_count',
        'type',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
            'discount_amount' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_discount' => 'decimal:2',
        ];
    }

    /**
     * Get the users that have this voucher
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_vouchers')
            ->withPivot('status', 'used_at', 'notified_at')
            ->withTimestamps();
    }

    /**
     * Check if voucher is still valid
     */
    public function isValid(): bool
    {
        return $this->is_active 
            && now()->date() >= $this->valid_from 
            && now()->date() <= $this->valid_until
            && ($this->max_uses === null || $this->used_count < $this->max_uses);
    }

    /**
     * Get discount value for a given amount
     */
    public function getDiscountValue(float $amount): float
    {
        $discount = 0;

        if ($this->discount_percentage) {
            $discount = ($amount * $this->discount_percentage) / 100;
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
        } elseif ($this->discount_amount) {
            $discount = $this->discount_amount;
        }

        return $discount;
    }
}
