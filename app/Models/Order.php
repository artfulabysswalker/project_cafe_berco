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
        'customer_name',
        'customer_phone',
        'table_id',
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
        'order_code',
        'paid_at',
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
        'paid_at' => 'datetime',
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

    /**
     * Kode tagihan publik (nomor antrean) untuk ditampilkan ke pelanggan.
     */
    public function getPublicCodeAttribute(): string
    {
        return $this->order_code ?? 'BT-'.str_pad((string) $this->id_order, 4, '0', STR_PAD_LEFT);
    }

    public function isPaid(): bool
    {
        return in_array($this->status_pembayaran, ['paid', 'Paid'], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status_order) {
            'pending' => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi Kasir',
            'processing' => 'Diproses Dapur',
            'ready' => 'Siap Diambil/Diantar',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst((string) $this->status_order),
        };
    }

    /**
     * Langkah timeline tracking pelanggan (urutan status yang distandarkan).
     */
    public function statusSteps(): array
    {
        $steps = [
            'pending' => [
                'key' => 'pending',
                'label' => 'Menunggu Pembayaran',
                'desc' => 'Pesanan menunggu konfirmasi pembayaran di kasir.',
            ],
            'processing' => [
                'key' => 'processing',
                'label' => 'Diproses Dapur',
                'desc' => 'Dapur sedang menyiapkan pesanan Anda.',
            ],
            'ready' => [
                'key' => 'ready',
                'label' => 'Siap Diambil/Diantar',
                'desc' => 'Pesanan siap diantarkan ke meja Anda.',
            ],
            'completed' => [
                'key' => 'completed',
                'label' => 'Selesai',
                'desc' => 'Pesanan telah selesai. Terima kasih!',
            ],
        ];

        return collect($steps)
            ->filter(function ($step) {
                return in_array($step['key'], ['pending', 'processing', 'ready', 'completed'], true);
            })
            ->values()
            ->all();
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

    public function table()
    {
        return $this->belongsTo(Meja::class, 'table_id', 'id_meja');
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
