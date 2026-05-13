<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'exp_cost',
        'discount_percentage',
        'available',
    ];

    protected $casts = [
        'exp_cost' => 'integer',
        'discount_percentage' => 'integer',
        'available' => 'boolean',
    ];

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }
}
