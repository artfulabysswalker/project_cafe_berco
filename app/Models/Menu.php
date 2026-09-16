<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'menus';

    protected $primaryKey = 'id_menu';

    protected $keyType = 'int';

    protected $fillable = [
        'nama_menu',
        'id_kategori',
        'series',
        'harga',
        'hpp',
        'stok',
        'status_tersedia',
        'foto',
        'rating',
        'deskripsi',
        'sub_variants',
        'has_temperature_option',
        'temperature_options',
        'flavor_options',
        'variant_selection_rules',
        'portion_count',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'hpp' => 'decimal:2',
        'rating' => 'decimal:1',
        'status_tersedia' => 'boolean',
        'has_temperature_option' => 'boolean',
        'sub_variants' => 'array',
        'temperature_options' => 'array',
        'flavor_options' => 'array',
        'variant_selection_rules' => 'array',
        'portion_count' => 'integer',
    ];

    public function getIdAttribute()
    {
        return $this->attributes['id_menu'] ?? null;
    }

    public function getNameAttribute()
    {
        return $this->attributes['nama_menu'] ?? null;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['harga'] ?? null;
    }

    public function getCostPriceAttribute()
    {
        return $this->attributes['hpp'] ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['deskripsi'] ?? null;
    }

    public function getImageUrlAttribute()
    {
        return $this->attributes['foto']
            ? '/storage/'.$this->attributes['foto']
            : null;
    }

    public function getCategoryAttribute()
    {
        return $this->categoryRelation?->nama_kategori ?? 'Umum';
    }

    public function getSlugAttribute()
    {
        return str()->slug($this->nama_menu);
    }

    public function getProfitMarginAttribute()
    {
        return max(0, ($this->harga ?? 0) - ($this->hpp ?? 0));
    }

    public function getProfitPercentageAttribute()
    {
        if (empty($this->harga) || $this->harga <= 0) {
            return 0;
        }

        return round((($this->harga - ($this->hpp ?? 0)) / $this->harga) * 100);
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes['id_kategori'] ?? null;
    }

    public function setCategoryIdAttribute($value)
    {
        $this->attributes['id_kategori'] = $value;
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) ($this->attributes['status_tersedia'] ?? true);
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status_tersedia'] = $value;
    }

    public function scopeActive($query)
    {
        return $query->where('status_tersedia', true);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->harga ?? 0, 0, ',', '.');
    }

    public function getPriceRangeFormattedAttribute(): string
    {
        if (! empty($this->temperature_options) && count($this->temperature_options) > 1) {
            $prices = array_column($this->temperature_options, 'price');
            $min = min($prices);
            $max = max($prices);
            if ($min !== $max) {
                return 'Rp '.number_format($min, 0, ',', '.').' - Rp '.number_format($max, 0, ',', '.');
            }
        }

        return $this->price_formatted;
    }

    public function hasStock(int $qty = 1): bool
    {
        return ($this->stok ?? 0) >= $qty;
    }

    public function decrementStock(int $qty = 1): bool
    {
        if ($this->stok < $qty) {
            $this->stok = 0;
            $this->status_tersedia = 0;
            $this->save();

            return false;
        }

        $this->stok = max(0, $this->stok - $qty);
        if ($this->stok === 0) {
            $this->status_tersedia = 0;
        }
        $this->save();

        return true;
    }

    public function incrementStock(int $qty = 1): void
    {
        $this->stok = ($this->stok ?? 0) + $qty;
        if ($this->stok > 0) {
            $this->status_tersedia = 1;
        }
        $this->save();
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'id_kategori', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_kategori', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_menu', 'id_menu');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'menu_id', 'id_menu');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class, 'menu_id', 'id_menu')->with('rawMaterial');
    }

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'product_recipes', 'menu_id', 'raw_material_id')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }

    /**
     * Hitung total HPP berdasarkan komposisi bahan baku
     */
    public function calculateRecipeHpp(): float
    {
        $total = 0;
        foreach ($this->recipes as $recipe) {
            if ($recipe->rawMaterial) {
                $total += (float) ($recipe->quantity_used * $recipe->rawMaterial->purchase_price);
            }
        }

        return round($total, 2);
    }

    /**
     * Update kolom HPP otomatis jika resep tersedia
     */
    public function updateHppFromRecipe(): bool
    {
        if ($this->recipes()->count() > 0) {
            $this->hpp = $this->calculateRecipeHpp();

            return $this->save();
        }

        return false;
    }
}
