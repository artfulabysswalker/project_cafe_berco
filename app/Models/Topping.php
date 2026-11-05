<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topping extends Model
{
    protected $fillable = [
        'name',
        'image',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ToppingSize::class);
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(
            Menu::class,
            'menu_toppings',
            'topping_id',
            'menu_id',
            'id',
            'id_menu'
        )->withTimestamps();
    }
}