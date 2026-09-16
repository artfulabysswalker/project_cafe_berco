<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'icon',
    ];

    protected $appends = ['name'];

    public function getNameAttribute(): ?string
    {
        return $this->nama_kategori;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nama_kategori'] = $value;
    }

    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug) && ! empty($category->nama_kategori)) {
                $category->slug = str()->slug($category->nama_kategori);
            }
        });
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'id_kategori', 'id');
    }

    public function products()
    {
        return $this->hasMany(Menu::class, 'id_kategori', 'id');
    }
}
