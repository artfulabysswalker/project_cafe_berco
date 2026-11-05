<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ToppingSize extends Model
{
    protected $fillable = [
        'id_topping',
        'size',
        'quantity',
        'unit',
        'harga',
    ];

    public function topping()
    {
        return $this->belongsTo(
            Topping::class,
            'id_topping'
        );
    }
}