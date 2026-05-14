<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_amount',
        'discount_percentage',
        'max_discount',
        'min_purchase',
        'max_uses',
        'type',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'status',
                'notified_at',
                'used_at'
            ])
            ->withTimestamps();
    }
}