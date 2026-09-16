<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipe extends Model
{
    protected $table = 'product_recipes';

    protected $fillable = [
        'menu_id',
        'raw_material_id',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:3',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id_menu');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id_menu');
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    /**
     * Subtotal modal per takaran bahan
     */
    public function getCostAttribute(): float
    {
        return (float) ($this->quantity_used * ($this->rawMaterial?->purchase_price ?? 0));
    }
}
