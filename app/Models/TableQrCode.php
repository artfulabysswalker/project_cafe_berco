<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class TableQrCode extends Model
{
    use HasFactory;

    protected $table = 'table_qr_codes';

    protected $primaryKey = 'id_qr_code';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'table_id',
        'token',
        'version',
        'expired_at',
        'is_active',
        'generated_by',
        'image_path',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_active' => 'boolean',
        'expired_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Meja::class, 'table_id', 'id_meja');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by', 'id_user');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query, Carbon|string|null $at = null): Builder
    {
        $now = $at ? Carbon::parse($at) : now();

        return $query->active()->where(function (Builder $q) use ($now) {
            $q->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        });
    }

    public function scopeExpired(Builder $query, Carbon|string|null $at = null): Builder
    {
        $now = $at ? Carbon::parse($at) : now();

        return $query->where(function (Builder $q) use ($now) {
            $q->where('expired_at', '<=', $now);
        });
    }

    public function scopeForTable(Builder $query, int $tableId): Builder
    {
        return $query->where('table_id', $tableId);
    }

    public function isExpired(Carbon|string|null $at = null): bool
    {
        if (is_null($this->expired_at)) {
            return false;
        }
        $now = $at ? Carbon::parse($at) : now();

        return $this->expired_at->lte($now);
    }

    public function getIdAttribute()
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }
}
