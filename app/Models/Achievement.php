<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'threshold',
        'reward_amount',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reward_amount' => 'decimal:2',
    ];

    /**
     * Get all users who earned this achievement
     */
    public function users(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
}
