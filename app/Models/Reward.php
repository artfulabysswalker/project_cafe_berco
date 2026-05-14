<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description',
        'exp_cost',
        'available',
    ];

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }
}