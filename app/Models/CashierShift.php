<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    use HasFactory;

    protected $table = 'cashier_shifts';

    protected $primaryKey = 'id_shift';

    protected $fillable = [
        'user_id',
        'shift_type',
        'starting_cash',
        'cash_sales',
        'non_cash_sales',
        'expected_cash',
        'actual_cash',
        'difference',
        'status',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'starting_cash' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'non_cash_sales' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Relasi ke kasir / user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Relasi ke daftar transaksi order pada shift ini
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_shift', 'id_shift');
    }

    /**
     * Scope: Filter shift yang aktif (open)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope: Filter shift berdasarkan tipe (shift_1 atau shift_2)
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('shift_type', $type);
    }

    // Accessor Aliases untuk kompatibilitas tampilan
    public function getNamaPegawaiAttribute(): string
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    public function getModalAwalAttribute()
    {
        return $this->starting_cash;
    }

    public function getTotalPenjualanTunaiAttribute()
    {
        return $this->cash_sales;
    }

    public function getTotalPenjualanNontunaiAttribute()
    {
        return $this->non_cash_sales;
    }

    public function getKasAkhirAktualAttribute()
    {
        return $this->actual_cash;
    }

    public function getSelisihAttribute()
    {
        return $this->difference;
    }
}
