<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVoucher extends Model
{
    /** @use HasFactory<\Database\Factories\UserVoucherFactory> */
    use HasFactory;

    protected $table = 'user_vouchers';

    protected $fillable = [
        'user_id',
        'voucher_id',
        'is_used',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'used_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the voucher
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Mark voucher as used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }
}
