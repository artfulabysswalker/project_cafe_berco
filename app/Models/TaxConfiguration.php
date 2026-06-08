<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxConfiguration extends Model
{
    protected $table = 'tax_configurations';
    protected $primaryKey = 'id_tax_config';
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'tax_percentage',
        'description',
        'is_active',
        'effective_from',
        'effective_until',
        'id_user',
    ];

    protected $casts = [
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_tax_config', 'id_tax_config');
    }

    /**
     * Get active tax configuration
     */
    public static function getActiveConfiguration()
    {
        $now = now();
        return self::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_from')->orWhere('effective_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $now);
            })
            ->first();
    }

    /**
     * Calculate tax amount based on subtotal
     */
    public function calculateTax($subtotal)
    {
        return ($subtotal * $this->tax_percentage) / 100;
    }
}
