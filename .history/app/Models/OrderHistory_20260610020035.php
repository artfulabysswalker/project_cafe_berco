<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    OrderHistory::create([
    'id_order' => $order->id_order,
    'id_user' => $order->id_user,
    'nama_pelanggan' => $order->nama_pelanggan,
    'total_harga' => $order->total_harga,
    'payment_method' => $order->payment_method,
    'service_type' => $order->service_type,
    'status_order' => 'completed',
    'status_pembayaran' => $order->status_pembayaran,
    'notes' => $order->notes,
]);

class OrderHistory extends Model
{
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
    ];
}
}
