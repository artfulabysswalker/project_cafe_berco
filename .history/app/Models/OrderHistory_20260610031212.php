<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $table = 'order_histories';



namespace App\Models;


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
        'tanggal',
    ];
}