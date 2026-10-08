<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meja extends Model
{
    use HasFactory;

    protected $table = 'mejas';

    protected $primaryKey = 'id_meja';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_meja',
        'kapasitas',
        'status',
        'area',
        'is_active',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
        'is_active' => 'boolean',
    ];

    public function qrCodes(): HasMany
    {
        return $this->hasMany(TableQrCode::class, 'table_id', 'id_meja');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id', 'id_meja');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getIdAttribute()
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }
}
