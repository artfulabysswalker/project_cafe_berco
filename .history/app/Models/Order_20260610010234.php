<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    protected $keyType = 'int';

    public function getRouteKeyName()
    {
        return 'id_order';
    }

    protected $fillable = [
        'tanggal',
        'nama_pelanggan',
        'total_harga',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'final_total',
        'status_pembayaran',
        'service_type',
        'payment_method',
        'notes',
        'status_order',
        'id_user',
        'id_tax_config',
        'id_discount_scheme',
        'cost_of_goods',
        'profit_margin',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'final_total' => 'decimal:2',
        'cost_of_goods' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
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
        return $this->belongsTo(TaxConfiguration::class, 'id_tax_config', 'id_tax_config');
    }

    public function discountScheme()
    {
        return $this->belongsTo(DiscountScheme::class, 'id_discount_scheme', 'id_discount_scheme');
    }



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'id_order';

    protected $fillable = [
        'tanggal',
        'nama_pelanggan',
        'total_harga',
        'status_pembayaran',
        'service_type',
        'payment_method',
        'notes',
        'status_order',
        'id_user',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];
}
}