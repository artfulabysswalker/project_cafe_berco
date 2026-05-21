<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'menu_id',
        'quantity',
    ];

    /**
     * User relation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    protected $table = 'cart_items';

    /**
     * Menu relation
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id_menu');
    }

    // compatibility accessor: some controllers expect ->menu_id
    public function getMenuIdAttribute()
    {
        return $this->attributes['menu_id'] ?? null;
    }

    public function getProductIdAttribute()
    {
        return $this->attributes['menu_id'] ?? null;
    }
}