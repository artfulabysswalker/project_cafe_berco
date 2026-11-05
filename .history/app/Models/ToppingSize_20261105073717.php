<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToppingSize extends Model
{
    protected $fillable = [
        'topping_id',
        'size',
        'quantity',
        'unit',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }
}