<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
public function order()
{
    return $this->belongsTo(Order::class, 'id_order', 'id_order');
}

public function menu()
{
    return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
}
}
