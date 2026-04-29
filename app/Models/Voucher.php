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
        'title',
        'description',
        'discount_type',
        'discount_value',
        'quantity',
        'quantity_used',
        'minimum_purchase',
        'valid_from',
        'valid_until',
        'is_active',
        'voucher_type',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users that have this voucher
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_vouchers')
            ->withPivot('assigned_at', 'used_at', 'is_used')
            ->withTimestamps();
    }

    /**
     * Check if voucher is still valid
     */
    public function isValid(): bool
    {
        return $this->is_active 
            && now()->between($this->valid_from, $this->valid_until)
            && (!$this->quantity || $this->quantity_used < $this->quantity);
    }

    /**
     * Check if voucher has quantity available
     */
    public function hasQuantityAvailable(): bool
    {
        return !$this->quantity || $this->quantity_used < $this->quantity;
    }

    /**
     * Assign voucher to a user
     */
    public function assignToUser(User $user): void
    {
        if (!$this->users()->where('users.id', $user->id)->exists()) {
            $this->users()->attach($user->id);
        }
    }
}
