<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountScheme extends Model
{
    protected $table = 'discount_schemes';
    protected $primaryKey = 'id_discount_scheme';
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'max_uses',
        'is_active',
        'valid_from',
        'valid_until',
        'id_user',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_discount_scheme', 'id_discount_scheme');
    }

    /**
     * Check if discount is valid and can be used
     */
    public function isValid($subtotal = 0)
    {
        $now = now();

        // Check if active
        if (!$this->is_active) {
            return ['valid' => false, 'reason' => 'Skema diskon tidak aktif'];
        }

        // Check if within valid period
        if ($now < $this->valid_from || $now > $this->valid_until) {
            return ['valid' => false, 'reason' => 'Skema diskon sudah kadaluarsa atau belum berlaku'];
        }

        // Check max uses
        if ($this->max_uses && $this->times_used >= $this->max_uses) {
            return ['valid' => false, 'reason' => 'Diskon sudah mencapai batas penggunaan'];
        }

        // Check minimum purchase
        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return ['valid' => false, 'reason' => 'Pembelian minimal Rp' . number_format($this->min_purchase, 0, ',', '.')];
        }

        return ['valid' => true];
    }

    /**
     * Calculate discount amount based on subtotal
     */
    public function calculateDiscount($subtotal)
    {
        if ($this->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount && $discountAmount > $this->max_discount) {
                $discountAmount = $this->max_discount;
            }
            return $discountAmount;
        } else {
            // Fixed amount
            return min($this->discount_value, $subtotal);
        }
    }

    /**
     * Get valid discount schemes at specific time
     */
    public static function getValidSchemes($subtotal = 0, $dateTime = null)
    {
        $dateTime = $dateTime ?? now();

        $schemes = self::where('is_active', true)
            ->where('valid_from', '<=', $dateTime)
            ->where('valid_until', '>=', $dateTime)
            ->get();

        return $schemes->filter(function ($scheme) use ($subtotal) {
            if (!$scheme->min_purchase) {
                return true;
            }
            return $subtotal >= $scheme->min_purchase;
        });
    }
}
