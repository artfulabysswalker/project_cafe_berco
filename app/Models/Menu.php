<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'nama_menu',
        'harga',
        'status_tersedia',
        'foto',
        'rating',
        'deskripsi',
    ];

    /**
     * Cart items relation
     */
    public function cartItems()
    {
        return $this->hasMany(
            CartItem::class,
            'menu_id',
            'id_menu'
        );
    }
    
}