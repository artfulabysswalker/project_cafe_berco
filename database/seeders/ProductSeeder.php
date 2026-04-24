<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Kopi
            [
                'name' => 'Espresso',
                'slug' => Str::slug('Espresso'),
                'description' => 'Shot kopi espresso murni',
                'category' => 'kopi',
                'price' => 12000,
                'image_url' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Americano',
                'slug' => Str::slug('Americano'),
                'description' => 'Espresso yang dilarutkan dengan air panas',
                'category' => 'kopi',
                'price' => 15000,
                'image_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Cappuccino',
                'slug' => Str::slug('Cappuccino'),
                'description' => 'Espresso dengan susu dan busa',
                'category' => 'kopi',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Latte',
                'slug' => Str::slug('Latte'),
                'description' => 'Espresso dengan susu hangat yang lembut',
                'category' => 'kopi',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1517668808822-9ebb02ae2a0e?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Macchiato',
                'slug' => Str::slug('Macchiato'),
                'description' => 'Espresso dengan sedikit susu dan busa',
                'category' => 'kopi',
                'price' => 16000,
                'image_url' => 'https://images.unsplash.com/photo-1585373866329-196dc7ffce31?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Flat White',
                'slug' => Str::slug('Flat White'),
                'description' => 'Espresso dengan microfoam susu yang halus',
                'category' => 'kopi',
                'price' => 19000,
                'image_url' => 'https://images.unsplash.com/photo-1514432324607-2e467f4af445?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Mocha',
                'slug' => Str::slug('Mocha'),
                'description' => 'Espresso dengan cokelat dan susu',
                'category' => 'kopi',
                'price' => 20000,
                'image_url' => 'https://images.unsplash.com/photo-1578880218149-51ede803fdf0?q=80&w=400',
                'available' => true,
            ],

            // Non Kopi
            [
                'name' => 'Mango Juice',
                'slug' => Str::slug('Mango Juice'),
                'description' => 'Jus mangga segar tanpa gula tambahan',
                'category' => 'non-kopi',
                'price' => 16000,
                'image_url' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Orange Juice',
                'slug' => Str::slug('Orange Juice'),
                'description' => 'Jus jeruk segar premium',
                'category' => 'non-kopi',
                'price' => 14000,
                'image_url' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Strawberry Juice',
                'slug' => Str::slug('Strawberry Juice'),
                'description' => 'Jus strawberry dengan madu alami',
                'category' => 'non-kopi',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Iced Tea',
                'slug' => Str::slug('Iced Tea'),
                'description' => 'Teh dingin segar dengan buah-buahan',
                'category' => 'non-kopi',
                'price' => 12000,
                'image_url' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?q=80&w=400',
                'available' => true,
            ],

            // Ice Blended
            [
                'name' => 'Iced Cappuccino Blend',
                'slug' => Str::slug('Iced Cappuccino Blend'),
                'description' => 'Cappuccino diblender dengan es dan susu',
                'category' => 'ice-blend',
                'price' => 20000,
                'image_url' => 'https://images.unsplash.com/photo-1461023058058-ada1f3c2d7d8?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Iced Mocha Blend',
                'slug' => Str::slug('Iced Mocha Blend'),
                'description' => 'Mocha diblender dengan es dan whipped cream',
                'category' => 'ice-blend',
                'price' => 22000,
                'image_url' => 'https://images.unsplash.com/photo-1578880218149-51ede803fdf0?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Caramel Blend',
                'slug' => Str::slug('Caramel Blend'),
                'description' => 'Blended caramel dengan espresso dan es',
                'category' => 'ice-blend',
                'price' => 21000,
                'image_url' => 'https://images.unsplash.com/photo-1578880218149-51ede803fdf0?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Vanilla Blend',
                'slug' => Str::slug('Vanilla Blend'),
                'description' => 'Blended vanilla dengan kopi dan susu',
                'category' => 'ice-blend',
                'price' => 20000,
                'image_url' => 'https://images.unsplash.com/photo-1578880218149-51ede803fdf0?q=80&w=400',
                'available' => true,
            ],

            // Snack
            [
                'name' => 'Croissant Coklat',
                'slug' => Str::slug('Croissant Coklat'),
                'description' => 'Croissant berbutter dengan cokelat leleh',
                'category' => 'snack',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Almond Croissant',
                'slug' => Str::slug('Almond Croissant'),
                'description' => 'Croissant dengan krim almond dan almond slices',
                'category' => 'snack',
                'price' => 22000,
                'image_url' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Danish Pastry',
                'slug' => Str::slug('Danish Pastry'),
                'description' => 'Pastry Denmark dengan topping buah-buahan',
                'category' => 'snack',
                'price' => 20000,
                'image_url' => 'https://images.unsplash.com/photo-1585365624948-cae8cf62bf65?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Cheese Bread',
                'slug' => Str::slug('Cheese Bread'),
                'description' => 'Roti dengan keju mozzarella yang gurih',
                'category' => 'snack',
                'price' => 15000,
                'image_url' => 'https://images.unsplash.com/photo-1585365624948-cae8cf62bf65?q=80&w=400',
                'available' => true,
            ],

            // Dessert
            [
                'name' => 'Tiramisu',
                'slug' => Str::slug('Tiramisu'),
                'description' => 'Tiramisu klasik Italia dengan mascarpone',
                'category' => 'dessert',
                'price' => 25000,
                'image_url' => 'https://images.unsplash.com/photo-1571115764595-644a262f6ae5?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Chocolate Cake',
                'slug' => Str::slug('Chocolate Cake'),
                'description' => 'Kue cokelat lezat dengan ganache cokelat',
                'category' => 'dessert',
                'price' => 22000,
                'image_url' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Cheesecake',
                'slug' => Str::slug('Cheesecake'),
                'description' => 'Cheesecake New York style yang creamy',
                'category' => 'dessert',
                'price' => 28000,
                'image_url' => 'https://images.unsplash.com/photo-1571115764595-644a262f6ae5?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Brownie',
                'slug' => Str::slug('Brownie'),
                'description' => 'Brownies coklat dengan nuts',
                'category' => 'dessert',
                'price' => 16000,
                'image_url' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?q=80&w=400',
                'available' => true,
            ],

            // Makanan
            [
                'name' => 'Sandwich Tuna',
                'slug' => Str::slug('Sandwich Tuna'),
                'description' => 'Sandwich tuna segar dengan sayuran',
                'category' => 'makanan',
                'price' => 19000,
                'image_url' => 'https://images.unsplash.com/photo-1541519227354-08fa5d50c44d?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Chicken Sandwich',
                'slug' => Str::slug('Chicken Sandwich'),
                'description' => 'Sandwich ayam panggang dengan mayo spesial',
                'category' => 'makanan',
                'price' => 21000,
                'image_url' => 'https://images.unsplash.com/photo-1541519227354-08fa5d50c44d?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Caesar Salad',
                'slug' => Str::slug('Caesar Salad'),
                'description' => 'Salad dengan dressing Caesar dan croutons',
                'category' => 'makanan',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400',
                'available' => true,
            ],
            [
                'name' => 'Pasta Carbonara',
                'slug' => Str::slug('Pasta Carbonara'),
                'description' => 'Pasta dengan saus carbonara klasik',
                'category' => 'makanan',
                'price' => 25000,
                'image_url' => 'https://images.unsplash.com/photo-1612874742237-6526221fcf2e?q=80&w=400',
                'available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
