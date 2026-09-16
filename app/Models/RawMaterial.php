<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    protected $table = 'raw_materials';

    protected $fillable = [
        'name',
        'unit',
        'purchase_price',
        'stock_quantity',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
    ];

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class, 'raw_material_id');
    }
}
