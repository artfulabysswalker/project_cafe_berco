<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'menu_id',
        'quantity',
        'temperature',
        'note',
        'notes',
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

    public function getUnitPriceAttribute(): float
    {
        if ($this->menu) {
            return (float) $this->menu->getPriceForTemperature($this->temperature);
        }

        return 0.0;
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->unit_price * $this->quantity);
    }

    public function getCartItemKeyAttribute(): string
    {
        return $this->menu_id.'_'.($this->temperature ?: 'default');
    }

    public function setNoteAttribute($value): void
    {
        $this->attributes['note'] = $value !== null ? mb_substr((string) $value, 0, 255) : null;
        $this->attributes['notes'] = $value;
    }

    public function setNotesAttribute($value): void
    {
        $this->setNoteAttribute($value);
    }
}
