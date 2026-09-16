<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds for standard Cafe Berco categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Classic Coffee',
                'slug' => 'classic-coffee',
                'icon' => 'fas fa-coffee',
            ],
            [
                'nama_kategori' => 'Non Coffee',
                'slug' => 'non-coffee',
                'icon' => 'fas fa-mug-hot',
            ],
            [
                'nama_kategori' => 'Food',
                'slug' => 'food',
                'icon' => 'fas fa-utensils',
            ],
            [
                'nama_kategori' => 'Snack',
                'slug' => 'snack',
                'icon' => 'fas fa-cookie-bite',
            ],
            [
                'nama_kategori' => 'Dessert',
                'slug' => 'dessert',
                'icon' => 'fas fa-cake-candles',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
