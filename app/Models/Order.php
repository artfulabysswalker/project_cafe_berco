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
        'status_pembayaran',
        'service_type',
        'payment_method',
        'notes',
        'status_order',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }

}