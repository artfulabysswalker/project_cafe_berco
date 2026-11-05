<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemTopping extends Model
{
    protected $fillable = [
        'id_order_item',
        'id_topping',
        'id_topping_size',
        'quantity',
        'unit',
        'harga',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'harga' => 'decimal:2',
    ];

    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class,
            'id_order_item'
        );
    }

    public function topping()
    {
        return $this->belongsTo(
            Topping::class,
            'id_topping'
        );
    }

    public function size()
    {
        return $this->belongsTo(
            ToppingSize::class,
            'id_topping_size'
        );
    }
}