<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    public $timestamps = true;

    protected $fillable = [
        'id_order',
        'snap_token',
        'payment_method',
        'metode_pembayaran',
        'amount',
        'jumlah_bayar',
        'status',
        'transaction_id',
        'tanggal_bayar',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
