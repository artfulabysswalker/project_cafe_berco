<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class BercoMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. SEED CATEGORIES
        $categoriesData = [
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

        $categoryMap = [];
        foreach ($categoriesData as $catData) {
            $cat = Category::firstOrCreate(
                ['nama_kategori' => $catData['nama_kategori']],
                $catData
            );
            $categoryMap[$catData['nama_kategori']] = $cat->id;
        }

        // 2. SEED RAW MATERIALS (Bahan Baku)
        $rawMaterialsData = [
            // Coffee & Bases
            ['name' => 'Espresso Blend (Beans)', 'unit' => 'gram', 'purchase_price' => 180.00, 'stock_quantity' => 25000, 'notes' => 'Blend Arabica & Robusta House Blend Berco'],
            ['name' => 'Air Mineral', 'unit' => 'ml', 'purchase_price' => 5.00, 'stock_quantity' => 50000, 'notes' => 'Air mineral filtrasi RO'],
            ['name' => 'Air Mineral & Es', 'unit' => 'ml', 'purchase_price' => 8.00, 'stock_quantity' => 50000, 'notes' => 'Air mineral + Ice cubes kristal'],
            ['name' => 'Air Soda', 'unit' => 'ml', 'purchase_price' => 15.00, 'stock_quantity' => 30000, 'notes' => 'Soda water / sparkling water'],
            ['name' => 'Air Soda & Es', 'unit' => 'ml', 'purchase_price' => 18.00, 'stock_quantity' => 30000, 'notes' => 'Soda water + Ice cubes kristal'],
            ['name' => 'Fresh Milk', 'unit' => 'ml', 'purchase_price' => 22.00, 'stock_quantity' => 40000, 'notes' => 'Pasteurized Fresh Milk Barista'],
            ['name' => 'Fresh Milk / Mix Base', 'unit' => 'ml', 'purchase_price' => 24.00, 'stock_quantity' => 30000, 'notes' => 'Fresh milk mix formula untuk matcha'],
            ['name' => 'Susu Racikan Khas Berco', 'unit' => 'ml', 'purchase_price' => 28.00, 'stock_quantity' => 25000, 'notes' => 'Secret blend creamy milk signature Berco'],
            ['name' => 'Susu Kental Manis / Soda', 'unit' => 'porsi', 'purchase_price' => 2500.00, 'stock_quantity' => 500, 'notes' => 'SKM / Soda porsi Lokal Pride'],
            ['name' => 'Bahan Seduh Tradisional', 'unit' => 'porsi', 'purchase_price' => 3000.00, 'stock_quantity' => 500, 'notes' => 'Ekstrak jahe / bahan racik tradisional'],

            // Syrups, Powders & Flavorings
            ['name' => 'Flavored Syrup / Aren', 'unit' => 'ml', 'purchase_price' => 150.00, 'stock_quantity' => 10000, 'notes' => 'Sirup perisa gourmet & gula aren organik'],
            ['name' => 'Sirup Lemon', 'unit' => 'ml', 'purchase_price' => 120.00, 'stock_quantity' => 6000, 'notes' => 'Lemon concentrate syrup'],
            ['name' => 'Fruit Syrup Concentrate', 'unit' => 'ml', 'purchase_price' => 130.00, 'stock_quantity' => 12000, 'notes' => 'Sirup buah (Strawberry, Lychee, Orange)'],
            ['name' => 'Biang Teh Hitam', 'unit' => 'ml', 'purchase_price' => 20.00, 'stock_quantity' => 25000, 'notes' => 'Seduhan konsentrat Black Tea premium'],
            ['name' => 'Sirup Buah Perisa', 'unit' => 'ml', 'purchase_price' => 140.00, 'stock_quantity' => 8000, 'notes' => 'Fruit flavored syrup for tea series'],
            ['name' => 'Bubuk Perisa Pilihan', 'unit' => 'gram', 'purchase_price' => 150.00, 'stock_quantity' => 10000, 'notes' => 'Powder drink (Chocolate, Taro, Green Tea)'],
            ['name' => 'Pure Matcha Powder', 'unit' => 'gram', 'purchase_price' => 500.00, 'stock_quantity' => 4000, 'notes' => 'Ceremonial grade pure matcha powder'],
            ['name' => 'Glaze Pilihan', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 8000, 'notes' => 'Glaze dipping donat aneka rasa'],

            // Food & Snack Ingredients
            ['name' => 'Donat Kentang Frozen', 'unit' => 'pcs', 'purchase_price' => 2000.00, 'stock_quantity' => 400, 'notes' => 'Donat kentang beku siap goreng'],
            ['name' => 'Risol Frozen', 'unit' => 'pcs', 'purchase_price' => 1800.00, 'stock_quantity' => 300, 'notes' => 'Risol beku siap goreng'],
            ['name' => 'Adonan Cireng Rujak', 'unit' => 'pcs', 'purchase_price' => 350.00, 'stock_quantity' => 1500, 'notes' => 'Cireng salju beku'],
            ['name' => 'Minyak Goreng', 'unit' => 'ml', 'purchase_price' => 20.00, 'stock_quantity' => 30000, 'notes' => 'Minyak goreng kelapa sawit premium'],
            ['name' => 'Kentang Beku', 'unit' => 'gram', 'purchase_price' => 30.00, 'stock_quantity' => 25000, 'notes' => 'Shoestring french fries frozen'],
            ['name' => 'Nugget Ayam Frozen', 'unit' => 'pcs', 'purchase_price' => 1200.00, 'stock_quantity' => 500, 'notes' => 'Chicken nugget beku'],
            ['name' => 'Sosis Sapi', 'unit' => 'pcs', 'purchase_price' => 1500.00, 'stock_quantity' => 400, 'notes' => 'Beef sausage jumbo'],
            ['name' => 'Nasi Putih', 'unit' => 'gram', 'purchase_price' => 15.00, 'stock_quantity' => 40000, 'notes' => 'Beras pulen matang'],
            ['name' => 'Bumbu Nasi Goreng', 'unit' => 'gram', 'purchase_price' => 70.00, 'stock_quantity' => 6000, 'notes' => 'Racikan bumbu nasi goreng jawa'],
            ['name' => 'Telur Ayam', 'unit' => 'butir', 'purchase_price' => 2200.00, 'stock_quantity' => 500, 'notes' => 'Telur ayam segar'],
            ['name' => 'Mie Telur', 'unit' => 'keping', 'purchase_price' => 2500.00, 'stock_quantity' => 300, 'notes' => 'Mie telur kering berstandar'],
            ['name' => 'Sayuran', 'unit' => 'gram', 'purchase_price' => 25.00, 'stock_quantity' => 10000, 'notes' => 'Sawi hijau, kol, daun bawang'],
            ['name' => 'Ayam Goreng Fillet', 'unit' => 'gram', 'purchase_price' => 55.00, 'stock_quantity' => 10000, 'notes' => 'Dada ayam fillet marinasi'],
            ['name' => 'Cabai Rawit Racik', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 5000, 'notes' => 'Racikan cabai rawit pedas chili padi'],

            // Packaging
            ['name' => 'Cup & Sedotan', 'unit' => 'pcs', 'purchase_price' => 1250.00, 'stock_quantity' => 5000, 'notes' => 'Cup Berco branded + lid + sedotan ramah lingkungan'],
        ];

        $materialMap = [];
        foreach ($rawMaterialsData as $matData) {
            $material = RawMaterial::firstOrCreate(
                ['name' => $matData['name']],
                $matData
            );
            $materialMap[$matData['name']] = $material->id;
        }

        // 3. PRODUCTS SPECIFICATION DATA
        $products = [
            // 1. Black Coffee
            [
                'category' => 'Classic Coffee',
                'name' => 'Black Coffee',
                'sub_variants' => ['Americano', 'Long Black'],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 6500],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 7500],
                ],
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Seduhan espresso murni dengan air mineral panas/dingin. Tersedia dalam sub-varian Americano atau Long Black.',
                'recipe' => [
                    ['ingredient' => 'Espresso Blend (Beans)', 'amount' => 18, 'unit' => 'gram'],
                    ['ingredient' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 2. White Coffee
            [
                'category' => 'Classic Coffee',
                'name' => 'White Coffee',
                'sub_variants' => ['Cappuccino', 'Caffe Latte', 'Magic'],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 8500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 8000],
                ],
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Kombinasi espresso seimbang dengan fresh milk steamed berbusa lembut (Cappuccino / Latte / Magic).',
                'recipe' => [
                    ['ingredient' => 'Espresso Blend (Beans)', 'amount' => 18, 'unit' => 'gram'],
                    ['ingredient' => 'Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 3. Kopi Susu Flavored
            [
                'category' => 'Classic Coffee',
                'name' => 'Kopi Susu Flavored',
                'sub_variants' => null,
                'flavor_options' => [
                    'Hazelnut',
                    'Caramel',
                    'Vanilla',
                    'Butterscotch',
                    'Gula Aren',
                    'Moccacino',
                ],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 22000, 'hpp' => 9500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 9000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Kopi susu istimewa dipadukan dengan sirup pilihan (Hazelnut, Caramel, Vanilla, Butterscotch, Gula Aren, Moccacino).',
                'recipe' => [
                    ['ingredient' => 'Espresso Blend (Beans)', 'amount' => 18, 'unit' => 'gram'],
                    ['ingredient' => 'Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                    ['ingredient' => 'Flavored Syrup / Aren', 'amount' => 20, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 4. Kopi Susu Berco (Signature)
            [
                'category' => 'Classic Coffee',
                'name' => 'Kopi Susu Berco (Signature)',
                'sub_variants' => null,
                'flavor_options' => null,
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 18000, 'hpp' => 7500],
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 7500],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Menu signature Cafe Berco dengan racikan susu khas yang creamy, legit, dan espresso bold yang nagih.',
                'recipe' => [
                    ['ingredient' => 'Espresso Blend (Beans)', 'amount' => 18, 'unit' => 'gram'],
                    ['ingredient' => 'Susu Racikan Khas Berco', 'amount' => 130, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 5. Lemonade Americano (Signature)
            [
                'category' => 'Classic Coffee',
                'name' => 'Lemonade Americano (Signature)',
                'sub_variants' => null,
                'flavor_options' => null,
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 18000, 'hpp' => 8000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Perpaduan segar sari lemon asli, sparkling soda dingin, dan double shot espresso aromatic.',
                'recipe' => [
                    ['ingredient' => 'Espresso Blend (Beans)', 'amount' => 18, 'unit' => 'gram'],
                    ['ingredient' => 'Sirup Lemon', 'amount' => 30, 'unit' => 'ml'],
                    ['ingredient' => 'Air Soda', 'amount' => 100, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 6. Summer (Non Coffee)
            [
                'category' => 'Non Coffee',
                'name' => 'Summer',
                'sub_variants' => null,
                'flavor_options' => ['Strawberry', 'Lychee', 'Orange'],
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Minuman buah menyegarkan ala musim panas dengan konsentrat buah pilihan (Strawberry, Lychee, Orange).',
                'recipe' => [
                    ['ingredient' => 'Fruit Syrup Concentrate', 'amount' => 30, 'unit' => 'ml'],
                    ['ingredient' => 'Air Mineral & Es', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 7. Sparkling Drink (Non Coffee)
            [
                'category' => 'Non Coffee',
                'name' => 'Sparkling Drink',
                'sub_variants' => null,
                'flavor_options' => ['Strawberry', 'Lychee', 'Orange'],
                'has_temperature_option' => false,
                'temperature_options' => [
                    ['type' => 'Cold', 'price' => 17000, 'hpp' => 6500],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Soda mocktail buah berkarbonasi dingin dengan sensasi meledak menyegarkan di tenggorokan.',
                'recipe' => [
                    ['ingredient' => 'Fruit Syrup Concentrate', 'amount' => 30, 'unit' => 'ml'],
                    ['ingredient' => 'Air Soda & Es', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 8. Tea (Non Coffee)
            [
                'category' => 'Non Coffee',
                'name' => 'Tea',
                'sub_variants' => null,
                'flavor_options' => ['Lychee Tea', 'Lemon Tea', 'Berry Tea'],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 15000, 'hpp' => 5000],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 5000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Seduhan teh hitam aromatik dengan infused sirup buah asli (Lychee, Lemon, Berry).',
                'recipe' => [
                    ['ingredient' => 'Biang Teh Hitam', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Sirup Buah Perisa', 'amount' => 20, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 9. Milkbase (Non Coffee)
            [
                'category' => 'Non Coffee',
                'name' => 'Milkbase',
                'sub_variants' => null,
                'flavor_options' => ['Chocolate', 'Taro', 'Green Tea'],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 7500],
                    ['type' => 'Cold', 'price' => 15000, 'hpp' => 7000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Minuman susu kaya rasa dengan pilihan cokelat premium, taro harum, atau green tea klasik.',
                'recipe' => [
                    ['ingredient' => 'Bubuk Perisa Pilihan', 'amount' => 25, 'unit' => 'gram'],
                    ['ingredient' => 'Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 10. Matcha Series (Non Coffee)
            [
                'category' => 'Non Coffee',
                'name' => 'Matcha Series',
                'sub_variants' => null,
                'flavor_options' => ['Latte', 'Strawberry', 'Orange', 'Matchacano'],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 20000, 'hpp' => 8000],
                    ['type' => 'Cold', 'price' => 20000, 'hpp' => 8000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Pure Uji Matcha autentik dengan berbagai kombinasi artisan (Latte, Strawberry Matcha, Orange, Matchacano).',
                'recipe' => [
                    ['ingredient' => 'Pure Matcha Powder', 'amount' => 15, 'unit' => 'gram'],
                    ['ingredient' => 'Fresh Milk / Mix Base', 'amount' => 150, 'unit' => 'ml'],
                    ['ingredient' => 'Cup & Sedotan', 'amount' => 1, 'unit' => 'pcs'],
                ],
            ],

            // 11. Lokal Pride (Non Coffee)
            [
                'category' => 'Non Coffee',
                'name' => 'Lokal Pride',
                'sub_variants' => null,
                'flavor_options' => ['Jahe Hangat', 'Susu Jahe', 'Jhosua', 'Sogem'],
                'has_temperature_option' => true,
                'temperature_options' => [
                    ['type' => 'Hot', 'price' => 10000, 'hpp' => 4000],
                    ['type' => 'Cold', 'price' => 12000, 'hpp' => 5000],
                ],
                'variant_selection_rules' => null,
                'portion_count' => 1,
                'deskripsi' => 'Minuman tradisional kearifan lokal legendaris (Jahe Hangat, Susu Jahe, Jhosua, Sogem).',
                'recipe' => [
                    ['ingredient' => 'Bahan Seduh Tradisional', 'amount' => 1, 'unit' => 'porsi'],
                    ['ingredient' => 'Susu Kental Manis / Soda', 'amount' => 1, 'unit' => 'porsi'],
                ],
            ],

            // 12. Donut Berco (Dessert)
            [
                'category' => 'Dessert',
                'name' => 'Donut Berco',
                'price' => 15000,
                'hpp' => 5500,
                'portion_count' => 2,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => [
                    'min_select' => 1,
                    'max_select' => 2,
                    'options' => ['Original', 'Chocolate', 'Matcha', 'Tiramisu'],
                ],
                'deskripsi' => 'Donat kentang empuk isi 2 pcs dengan pilihan glaze premium (Original, Chocolate, Matcha, Tiramisu).',
                'recipe' => [
                    ['ingredient' => 'Donat Kentang Frozen', 'amount' => 2, 'unit' => 'pcs'],
                    ['ingredient' => 'Glaze Pilihan', 'amount' => 25, 'unit' => 'gram'],
                ],
            ],

            // 13. Risol (Snack)
            [
                'category' => 'Snack',
                'name' => 'Risol',
                'price' => 15000,
                'hpp' => 6500,
                'portion_count' => 3,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => [
                    'min_select' => 1,
                    'max_select' => 1,
                    'options' => ['Original', 'Mayones'],
                ],
                'deskripsi' => 'Risol renyah isi 3 pcs dengan saus cocolan nikmat (Original / Mayones).',
                'recipe' => [
                    ['ingredient' => 'Risol Frozen', 'amount' => 3, 'unit' => 'pcs'],
                    ['ingredient' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ],
            ],

            // 14. Cireng Salju (Snack)
            [
                'category' => 'Snack',
                'name' => 'Cireng Salju',
                'price' => 12000,
                'hpp' => 4500,
                'portion_count' => 10,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => [
                    'min_select' => 1,
                    'max_select' => 1,
                    'options' => ['Original', 'Saus Bangkok'],
                ],
                'deskripsi' => 'Cireng kenyal gurih 10 pcs dengan bumbu rujak pedas manis atau saus bangkok.',
                'recipe' => [
                    ['ingredient' => 'Adonan Cireng Rujak', 'amount' => 10, 'unit' => 'pcs'],
                    ['ingredient' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ],
            ],

            // 15. French Fries (Snack)
            [
                'category' => 'Snack',
                'name' => 'French Fries',
                'price' => 12000,
                'hpp' => 5000,
                'portion_count' => 1,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'deskripsi' => 'Kentang goreng shoestring renyah bertabur bumbu gurih khas Berco.',
                'recipe' => [
                    ['ingredient' => 'Kentang Beku', 'amount' => 150, 'unit' => 'gram'],
                    ['ingredient' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ],
            ],

            // 16. Mix Snack (Snack)
            [
                'category' => 'Snack',
                'name' => 'Mix Snack',
                'price' => 20000,
                'hpp' => 8500,
                'portion_count' => 1,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'deskripsi' => 'Platter kombinasi kentang goreng, 3 pcs chicken nugget, dan 2 pcs sosis panggang.',
                'recipe' => [
                    ['ingredient' => 'Kentang Beku', 'amount' => 80, 'unit' => 'gram'],
                    ['ingredient' => 'Nugget Ayam Frozen', 'amount' => 3, 'unit' => 'pcs'],
                    ['ingredient' => 'Sosis Sapi', 'amount' => 2, 'unit' => 'pcs'],
                ],
            ],

            // 17. Nasi Goreng Jawa (Food)
            [
                'category' => 'Food',
                'name' => 'Nasi Goreng Jawa',
                'price' => 20000,
                'hpp' => 8500,
                'portion_count' => 1,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'deskripsi' => 'Nasi goreng bumbu rempah tradisional Jawa dengan telur mata sapi dan acar.',
                'recipe' => [
                    ['ingredient' => 'Nasi Putih', 'amount' => 200, 'unit' => 'gram'],
                    ['ingredient' => 'Bumbu Nasi Goreng', 'amount' => 30, 'unit' => 'gram'],
                    ['ingredient' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                ],
            ],

            // 18. Mie Nyemek (Food)
            [
                'category' => 'Food',
                'name' => 'Mie Nyemek',
                'price' => 15000,
                'hpp' => 6500,
                'portion_count' => 1,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'deskripsi' => 'Mie kuah kental gurih khas nusantara dimasak dengan telur orak-arik dan sayuran segar.',
                'recipe' => [
                    ['ingredient' => 'Mie Telur', 'amount' => 1, 'unit' => 'keping'],
                    ['ingredient' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                    ['ingredient' => 'Sayuran', 'amount' => 40, 'unit' => 'gram'],
                ],
            ],

            // 19. Ayam Chili Padi (Food)
            [
                'category' => 'Food',
                'name' => 'Ayam Chili Padi',
                'price' => 20000,
                'hpp' => 9000,
                'portion_count' => 1,
                'has_temperature_option' => false,
                'temperature_options' => null,
                'sub_variants' => null,
                'flavor_options' => null,
                'variant_selection_rules' => null,
                'deskripsi' => 'Ayam goreng renyah bumbu chili padi pedas gurih disajikan hangat bersama seporsi nasi putih.',
                'recipe' => [
                    ['ingredient' => 'Nasi Putih', 'amount' => 200, 'unit' => 'gram'],
                    ['ingredient' => 'Ayam Goreng Fillet', 'amount' => 100, 'unit' => 'gram'],
                    ['ingredient' => 'Cabai Rawit Racik', 'amount' => 35, 'unit' => 'gram'],
                ],
            ],
        ];

        // 4. INSERT / UPDATE MENUS & PRODUCT RECIPES
        foreach ($products as $item) {
            $catId = $categoryMap[$item['category']] ?? null;

            // Determine default display price & HPP
            $defaultPrice = $item['price'] ?? 0;
            $defaultHpp = $item['hpp'] ?? 0;

            if (! empty($item['temperature_options'])) {
                // If Hot is available, use Hot as primary base price/hpp, else first option
                $firstTemp = $item['temperature_options'][0];
                foreach ($item['temperature_options'] as $tOpt) {
                    if (strcasecmp($tOpt['type'], 'Hot') === 0) {
                        $firstTemp = $tOpt;
                        break;
                    }
                }
                $defaultPrice = $firstTemp['price'] ?? $defaultPrice;
                $defaultHpp = $firstTemp['hpp'] ?? $defaultHpp;
            }

            // Find or create Menu
            $menu = Menu::updateOrCreate(
                ['nama_menu' => $item['name']],
                [
                    'id_kategori' => $catId,
                    'harga' => $defaultPrice,
                    'hpp' => $defaultHpp,
                    'stok' => 50,
                    'status_tersedia' => 1,
                    'deskripsi' => $item['deskripsi'] ?? '',
                    'sub_variants' => $item['sub_variants'] ?? null,
                    'has_temperature_option' => $item['has_temperature_option'] ?? false,
                    'temperature_options' => $item['temperature_options'] ?? null,
                    'flavor_options' => $item['flavor_options'] ?? null,
                    'variant_selection_rules' => $item['variant_selection_rules'] ?? null,
                    'portion_count' => $item['portion_count'] ?? 1,
                ]
            );

            // Seed Product Recipes
            if (! empty($item['recipe'])) {
                $menu->recipes()->delete(); // Clear existing recipe links for clean sync

                foreach ($item['recipe'] as $rec) {
                    $matId = $materialMap[$rec['ingredient']] ?? null;
                    if ($matId) {
                        ProductRecipe::create([
                            'menu_id' => $menu->id_menu,
                            'raw_material_id' => $matId,
                            'quantity_used' => $rec['amount'],
                        ]);
                    }
                }
            }
        }
    }
}
