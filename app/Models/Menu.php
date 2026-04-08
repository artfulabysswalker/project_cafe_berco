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
        'status_tersedia'
    ];

    // FIX: filter biar tidak error
    public function scopeFilter($query, $request)
    {
        if ($request->search) {
            $query->where('nama_menu', 'like', '%' . $request->search . '%');
        }

        if ($request->price_filter === 'low') {
            $query->where('harga', '<', 15000);
        }

        if ($request->price_filter === 'high') {
            $query->where('harga', '>=', 15000);
        }

        if ($request->max_price) {
            $query->where('harga', '<=', $request->max_price);
        }

        return $query;
    }
}