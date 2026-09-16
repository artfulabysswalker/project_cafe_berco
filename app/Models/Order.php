<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'id_order';

    protected $keyType = 'int';

    protected $fillable = [
        'tanggal',
        'nama_pelanggan',
        'total_harga',
        'subtotal',
        'tax_amount',
        'service_charge',
        'discount_amount',
        'final_total',
        'status_pembayaran',
        'service_type',
        'payment_method',
        'notes',
        'status_order',
        'id_user',
        'id_shift',
        'cashier_name',
        'id_tax_config',
        'id_discount_scheme',
        'cost_of_goods',
        'profit_margin',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'final_total' => 'decimal:2',
        'cost_of_goods' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    public function getRouteKeyName()
    {
        return 'id_order';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function shift()
    {
        return $this->belongsTo(CashierShift::class, 'id_shift', 'id_shift');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'id_order', 'id_order');
    }

    public function taxConfiguration()
    {
        return $this->belongsTo(
            TaxConfiguration::class,
            'id_tax_config',
            'id_tax_config'
        );
    }

    public function discountScheme()
    {
        return $this->belongsTo(
            DiscountScheme::class,
            'id_discount_scheme',
            'id_discount_scheme'
        );
    }

    /**
     * Scope untuk isolasi data:
     * - Kasir: Terisolasi ke id_shift aktif atau id_user sendiri.
     * - Admin: Membuka akses penuh dengan filter opsional (shift_type, user_id, date).
     */
    public function scopeForAuthorizedUser($query, User $user, array $filters = [])
    {
        if (! $user->isAdmin()) {
            $activeShift = $user->activeShift;

            return $query->where('id_user', $user->id_user)
                ->when($activeShift, function ($q) use ($activeShift) {
                    $q->where('id_shift', $activeShift->id_shift);
                });
        }

        // Filter untuk Admin / Owner
        return $query
            ->when(! empty($filters['shift_type']), function ($q) use ($filters) {
                $q->whereHas('shift', function ($sq) use ($filters) {
                    $sq->where('shift_type', $filters['shift_type']);
                });
            })
            ->when(! empty($filters['user_id']), function ($q) use ($filters) {
                $q->where('id_user', $filters['user_id']);
            })
            ->when(! empty($filters['date']), function ($q) use ($filters) {
                $q->whereDate('tanggal', $filters['date']);
            });
    }
}
