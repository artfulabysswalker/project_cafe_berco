<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'item_code',
        'item_name',
        'category',
        'current_stock',
        'unit',
        'min_stock',
        'status',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    /**
     * Booted model listener: automatically compute status on save
     */
    protected static function booted()
    {
        static::saving(function ($inventory) {
            $current = (float) $inventory->current_stock;
            $min = (float) $inventory->min_stock;

            if ($min <= 0) {
                $inventory->status = $current > 0 ? 'aman' : 'kritis';
            } elseif ($current <= ($min * 0.5)) {
                $inventory->status = 'kritis';
            } elseif ($current <= $min) {
                $inventory->status = 'menipis';
            } else {
                $inventory->status = 'aman';
            }
        });
    }

    /**
     * Accessor: Badge CSS color class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'kritis' => 'bg-red-50 text-red-700 border-red-200',
            'menipis' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }

    /**
     * Accessor: Status Display Label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'kritis' => 'Kritis (Perlu Restock Segera)',
            'menipis' => 'Menipis (Mendekati Batas Min)',
            default => 'Stok Aman',
        };
    }
}
