<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id_menu';
    protected $keyType = 'int';

    protected $fillable = [
        'nama_menu',
        'harga',
        'status_tersedia',
        'foto',
        'rating',
        'deskripsi',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['id_menu'] ?? null;
    }

    public function getNameAttribute()
    {
        return $this->attributes['nama_menu'] ?? null;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['harga'] ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['deskripsi'] ?? null;
    }

    public function getImageUrlAttribute()
    {
        return $this->attributes['foto']
            ? '/storage/' . $this->attributes['foto']
            : null;
    }

    public function getCategoryAttribute()
    {
        return 'menu';
    }

    public function getSlugAttribute()
    {
        return str()->slug($this->nama_menu);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_menu', 'id_menu');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'menu_id', 'id_menu');
    }
    public function getFinalPriceAttribute()
{
    if ($this->discount_price &&
        now()->between($this->discount_start, $this->discount_end)) {
        return $this->discount_price;
    }

    return $this->harga;
}
}
