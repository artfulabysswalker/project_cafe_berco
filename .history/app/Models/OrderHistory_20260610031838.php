<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $table = 'order_histories';

    // IMPORTANT: allows mass assignment safely
    protected $fillable = [
        'id_order',
        'id_user',
        'nama_pelanggan',
        'total_harga',
        'payment_method',
        'service_type',
        'status_order',
        'status_pembayaran',
        'notes',
        'tanggal',
    ];

    // 🔥 FIX: make date work properly in Blade (format(), etc.)
    protected $casts = [
        'tanggal' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Optional but useful
    public $timestamps = true;
    return $this->belongsTo(OrderHistory::class, 'id_order', 'id_order');
}
}