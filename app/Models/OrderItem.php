<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'id_order',
        'id_menu',
        'quantity',
        'subtotal',
        'hpp',
        'hpp_at_sale',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'hpp' => 'decimal:2',
        'hpp_at_sale' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }

    public function product()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }

    public function getEffectiveHppAttribute()
    {
        if (! empty($this->hpp_at_sale) && $this->hpp_at_sale > 0) {
            return (float) $this->hpp_at_sale;
        }
        if (! empty($this->hpp) && $this->hpp > 0) {
            return (float) $this->hpp;
        }

        return (float) ($this->menu?->hpp ?? 0);
    }

    public function getTotalHppAttribute()
    {
        return $this->effective_hpp * $this->quantity;
    }

    public function getProfitAttribute()
    {
        return max(0, $this->subtotal - $this->total_hpp);
    }
}
