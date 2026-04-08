<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'menu_id',
        'qty'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id_menu');
    }

    // Method untuk menghitung subtotal per item
    public function getSubtotalAttribute()
    {
        return $this->qty * $this->menu->harga;
    }
}