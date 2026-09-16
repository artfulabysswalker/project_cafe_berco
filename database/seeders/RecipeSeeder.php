<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds for Menu Recipes & Raw Material Compositions.
     */
    public function run(): void
    {
        // 1. MASTER INGREDIENTS / RAW MATERIALS (Bahan Baku)
        $ingredientsMaster = [
            // Dessert & Bakery Ingredients
            ['name' => 'Pisang Kepok', 'unit' => 'buah', 'purchase_price' => 1500.00, 'stock_quantity' => 100, 'notes' => 'Pisang kepok matang per buah'],
            ['name' => 'Cokelat Cair / Meises', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 5000, 'notes' => 'Cokelat lumer & meses topping'],
            ['name' => 'Keju Parut', 'unit' => 'gram', 'purchase_price' => 100.00, 'stock_quantity' => 3000, 'notes' => 'Keju cheddar parut'],
            ['name' => 'Adonan Churros', 'unit' => 'pcs', 'purchase_price' => 800.00, 'stock_quantity' => 200, 'notes' => 'Churros dough siap goreng'],
            ['name' => 'Gula Cinnamon', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 2000, 'notes' => 'Gula pasir + kayu manis bubuk'],
            ['name' => 'Dipping Saus Cokelat', 'unit' => 'ml', 'purchase_price' => 70.00, 'stock_quantity' => 3000, 'notes' => 'Saus cokelat cocolan'],
            ['name' => 'Donat Kentang Frozen', 'unit' => 'pcs', 'purchase_price' => 2000.00, 'stock_quantity' => 250, 'notes' => 'Donat kentang beku siap goreng'],
            ['name' => 'Gula Halus / Glaze Original', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 2000, 'notes' => 'Gula salju tabur donat'],
            ['name' => 'Glaze Chocolate', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 2500, 'notes' => 'Glaze rasa cokelat'],
            ['name' => 'Glaze Matcha', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 2500, 'notes' => 'Glaze rasa matcha'],
            ['name' => 'Glaze Tiramisu', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 2500, 'notes' => 'Glaze rasa tiramisu'],

            // Snack & Finger Food Ingredients
            ['name' => 'Sosis Bakar Jumbo', 'unit' => 'pcs', 'purchase_price' => 2500.00, 'stock_quantity' => 150, 'notes' => 'Sosis sapi premium jumbo'],
            ['name' => 'Kentang Goreng', 'unit' => 'gram', 'purchase_price' => 30.00, 'stock_quantity' => 10000, 'notes' => 'Shoestring / Straight cut fries'],
            ['name' => 'Kentang Beku', 'unit' => 'gram', 'purchase_price' => 30.00, 'stock_quantity' => 15000, 'notes' => 'Kentang beku impor'],
            ['name' => 'Saus BBQ & Mayones', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 3000, 'notes' => 'Racikan saus BBQ & mayones'],
            ['name' => 'Tahu Pong Goreng', 'unit' => 'pcs', 'purchase_price' => 500.00, 'stock_quantity' => 300, 'notes' => 'Tahu pong kopong'],
            ['name' => 'Bumbu Petis', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 3000, 'notes' => 'Petis udang bumbu khas'],
            ['name' => 'Cabai Rawit Hijau', 'unit' => 'pcs', 'purchase_price' => 150.00, 'stock_quantity' => 1000, 'notes' => 'Cabai lalapan segar'],
            ['name' => 'Tahu Walik Ayam', 'unit' => 'pcs', 'purchase_price' => 800.00, 'stock_quantity' => 250, 'notes' => 'Tahu walik adonan bakso ayam'],
            ['name' => 'Minyak Goreng', 'unit' => 'ml', 'purchase_price' => 18.00, 'stock_quantity' => 30000, 'notes' => 'Minyak goreng kelapa sawit'],
            ['name' => 'Sambal Kecap', 'unit' => 'ml', 'purchase_price' => 30.00, 'stock_quantity' => 2000, 'notes' => 'Kecap manis + rawit potong'],
            ['name' => 'Adonan Cireng', 'unit' => 'pcs', 'purchase_price' => 350.00, 'stock_quantity' => 500, 'notes' => 'Cireng salju siap goreng'],
            ['name' => 'Saus Bangkok', 'unit' => 'ml', 'purchase_price' => 40.00, 'stock_quantity' => 2000, 'notes' => 'Saus asam manis pedas Bangkok'],
            ['name' => 'Risol Frozen', 'unit' => 'pcs', 'purchase_price' => 1500.00, 'stock_quantity' => 150, 'notes' => 'Risol sayur beku'],
            ['name' => 'Risol Mayo Frozen', 'unit' => 'pcs', 'purchase_price' => 1800.00, 'stock_quantity' => 150, 'notes' => 'Risol mayo smoked beef'],
            ['name' => 'Cabai Rawit', 'unit' => 'pcs', 'purchase_price' => 150.00, 'stock_quantity' => 1000, 'notes' => 'Cabai rawit segar'],
            ['name' => 'Lumpia Frozen', 'unit' => 'pcs', 'purchase_price' => 1500.00, 'stock_quantity' => 150, 'notes' => 'Lumpia sayur beku'],
            ['name' => 'Saus Acar', 'unit' => 'porsi', 'purchase_price' => 800.00, 'stock_quantity' => 100, 'notes' => 'Acar mentimun + saus tauco'],
            ['name' => 'Saus Sachet', 'unit' => 'pcs', 'purchase_price' => 400.00, 'stock_quantity' => 500, 'notes' => 'Saus sambal sachet'],
            ['name' => 'Nugget Ayam Frozen', 'unit' => 'pcs', 'purchase_price' => 700.00, 'stock_quantity' => 300, 'notes' => 'Nugget ayam beku'],
            ['name' => 'Nugget Ayam', 'unit' => 'pcs', 'purchase_price' => 700.00, 'stock_quantity' => 300, 'notes' => 'Nugget ayam siap goreng'],
            ['name' => 'Sosis Sapi', 'unit' => 'pcs', 'purchase_price' => 2000.00, 'stock_quantity' => 200, 'notes' => 'Sosis sapi grill'],

            // Food / Kitchen Ingredients
            ['name' => 'Beras / Nasi Putih', 'unit' => 'gram', 'purchase_price' => 16.00, 'stock_quantity' => 20000, 'notes' => 'Beras pulen / nasi porsi'],
            ['name' => 'Telur Ayam', 'unit' => 'butir', 'purchase_price' => 2000.00, 'stock_quantity' => 300, 'notes' => 'Telur ayam negeri per butir'],
            ['name' => 'Bumbu Nasi Goreng', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 5000, 'notes' => 'Bumbu racik nasgor spesial'],
            ['name' => 'Daging Ayam Suwir', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 4000, 'notes' => 'Daging dada ayam rebus suwir'],
            ['name' => 'Udang & Cumi', 'unit' => 'gram', 'purchase_price' => 100.00, 'stock_quantity' => 3000, 'notes' => 'Seafood segar potong'],
            ['name' => 'Mie Telur', 'unit' => 'keping', 'purchase_price' => 2500.00, 'stock_quantity' => 150, 'notes' => 'Mie kuning telur per keping'],
            ['name' => 'Sayuran Kol/Sawi', 'unit' => 'gram', 'purchase_price' => 20.00, 'stock_quantity' => 5000, 'notes' => 'Sawi hijau & kol iris segar'],
            ['name' => 'Bumbu Racik', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 3000, 'notes' => 'Bumbu mie nyemek & goreng'],
            ['name' => 'Kwetiau Basah', 'unit' => 'gram', 'purchase_price' => 20.00, 'stock_quantity' => 5000, 'notes' => 'Kwetiau beras basah segar'],
            ['name' => 'Sosis / Ayam', 'unit' => 'gram', 'purchase_price' => 60.00, 'stock_quantity' => 3000, 'notes' => 'Potongan sosis & ayam'],
            ['name' => 'Sayuran & Bumbu', 'unit' => 'gram', 'purchase_price' => 30.00, 'stock_quantity' => 4000, 'notes' => 'Sayuran & bumbu tumis kwetiau'],
            ['name' => 'Daging Ayam Fillet', 'unit' => 'gram', 'purchase_price' => 55.00, 'stock_quantity' => 8000, 'notes' => 'Fillet dada/paha ayam segar'],
            ['name' => 'Saus Lada Hitam', 'unit' => 'gram', 'purchase_price' => 45.00, 'stock_quantity' => 3000, 'notes' => 'Saus blackpepper aromatic'],
            ['name' => 'Bawang Bombay', 'unit' => 'gram', 'purchase_price' => 30.00, 'stock_quantity' => 3000, 'notes' => 'Bawang bombay iris'],
            ['name' => 'Cabai Rawit Merah & Hijau', 'unit' => 'gram', 'purchase_price' => 40.00, 'stock_quantity' => 3000, 'notes' => 'Cabai rawit ulek chili padi'],

            // Coffee & Beverage Ingredients
            ['name' => 'Biji Kopi House Blend', 'unit' => 'gram', 'purchase_price' => 140.00, 'stock_quantity' => 20000, 'notes' => 'Rp 140.000/kg House Blend'],
            ['name' => 'Air Mineral', 'unit' => 'ml', 'purchase_price' => 5.00, 'stock_quantity' => 50000, 'notes' => 'Air mineral filter RO'],
            ['name' => 'Susu Fresh Milk', 'unit' => 'ml', 'purchase_price' => 24.00, 'stock_quantity' => 30000, 'notes' => 'Fresh Milk pasteurisasi'],
            ['name' => 'Cup (Paper/Plastic)', 'unit' => 'pcs', 'purchase_price' => 1250.00, 'stock_quantity' => 3000, 'notes' => 'Cup + Lid + Straw / Seal'],
            ['name' => 'Sirup Flavour Terkait', 'unit' => 'ml', 'purchase_price' => 150.00, 'stock_quantity' => 5000, 'notes' => 'Flavored syrup (Hazelnut/Caramel/Vanilla/etc)'],
            ['name' => 'Susu Racikan Khas Berco', 'unit' => 'ml', 'purchase_price' => 26.00, 'stock_quantity' => 15000, 'notes' => 'Racikan fresh milk & creamer khas Berco'],
            ['name' => 'Sirup Lemon', 'unit' => 'ml', 'purchase_price' => 70.00, 'stock_quantity' => 3000, 'notes' => 'Konsentrat sari lemon asli'],
            ['name' => 'Sirup Konsentrat Buah', 'unit' => 'ml', 'purchase_price' => 70.00, 'stock_quantity' => 5000, 'notes' => 'Konsentrat sari buah segar'],
            ['name' => 'Air Soda', 'unit' => 'ml', 'purchase_price' => 12.00, 'stock_quantity' => 20000, 'notes' => 'Air soda berkarbonasi dingin'],
            ['name' => 'Biang Teh Hitam', 'unit' => 'ml', 'purchase_price' => 8.00, 'stock_quantity' => 15000, 'notes' => 'Seduhan teh hitam pekat aromatik'],
            ['name' => 'Bubuk Perisa Pilihan', 'unit' => 'gram', 'purchase_price' => 120.00, 'stock_quantity' => 5000, 'notes' => 'Bubuk Choco / Taro / Green Tea'],
            ['name' => 'Pure Matcha Powder', 'unit' => 'gram', 'purchase_price' => 250.00, 'stock_quantity' => 3000, 'notes' => 'Pure Japanese Matcha Powder Uji'],
            ['name' => 'Biang Jahe Rempah', 'unit' => 'ml', 'purchase_price' => 20.00, 'stock_quantity' => 5000, 'notes' => 'Rebusan sari jahe & rempah asli'],
            ['name' => 'Susu Kental Manis', 'unit' => 'ml', 'purchase_price' => 28.00, 'stock_quantity' => 6000, 'notes' => 'Susu kental manis gurih'],
            ['name' => 'Extra Joss Sachet', 'unit' => 'pcs', 'purchase_price' => 1500.00, 'stock_quantity' => 200, 'notes' => 'Extra joss serbuk sachet'],
            ['name' => 'Sirup Cocopandan', 'unit' => 'ml', 'purchase_price' => 50.00, 'stock_quantity' => 3000, 'notes' => 'Sirup merah cocopandan manis'],
        ];

        // 2. SAVE ALL INGREDIENTS TO DATABASE
        $savedIngredients = [];
        foreach ($ingredientsMaster as $item) {
            $ing = RawMaterial::firstOrCreate(
                ['name' => $item['name']],
                [
                    'unit' => $item['unit'],
                    'purchase_price' => $item['purchase_price'],
                    'stock_quantity' => $item['stock_quantity'],
                    'notes' => $item['notes'],
                ]
            );
            $savedIngredients[$item['name']] = $ing;
        }

        // 3. RECIPES MAPPING FOR ALL 55 CAFE BERCO PRODUCTS
        $recipesData = [
            // ==========================================
            // 1. DESSERT
            // ==========================================
            'Pisang Chocolate Original' => [
                ['name' => 'Pisang Kepok', 'amount' => 2, 'unit' => 'buah'],
                ['name' => 'Cokelat Cair / Meises', 'amount' => 25, 'unit' => 'gram'],
                ['name' => 'Keju Parut', 'amount' => 15, 'unit' => 'gram'],
            ],
            'Churros Original' => [
                ['name' => 'Adonan Churros', 'amount' => 5, 'unit' => 'pcs'],
                ['name' => 'Gula Cinnamon', 'amount' => 15, 'unit' => 'gram'],
                ['name' => 'Dipping Saus Cokelat', 'amount' => 30, 'unit' => 'ml'],
            ],
            'Donut Berco Original' => [
                ['name' => 'Donat Kentang Frozen', 'amount' => 2, 'unit' => 'pcs'],
                ['name' => 'Gula Halus / Glaze Original', 'amount' => 20, 'unit' => 'gram'],
            ],
            'Donut Berco Chocolate' => [
                ['name' => 'Donat Kentang Frozen', 'amount' => 2, 'unit' => 'pcs'],
                ['name' => 'Glaze Chocolate', 'amount' => 25, 'unit' => 'gram'],
            ],
            'Donut Berco Matcha' => [
                ['name' => 'Donat Kentang Frozen', 'amount' => 2, 'unit' => 'pcs'],
                ['name' => 'Glaze Matcha', 'amount' => 25, 'unit' => 'gram'],
            ],
            'Donut Berco Tiramisu' => [
                ['name' => 'Donat Kentang Frozen', 'amount' => 2, 'unit' => 'pcs'],
                ['name' => 'Glaze Tiramisu', 'amount' => 25, 'unit' => 'gram'],
            ],

            // ==========================================
            // 2. SNACK
            // ==========================================
            'Sosis Bakar' => [
                ['name' => 'Sosis Bakar Jumbo', 'amount' => 3, 'unit' => 'pcs'],
                ['name' => 'Kentang Goreng', 'amount' => 80, 'unit' => 'gram'],
                ['name' => 'Saus BBQ & Mayones', 'amount' => 30, 'unit' => 'gram'],
            ],
            'Tahu Petis' => [
                ['name' => 'Tahu Pong Goreng', 'amount' => 6, 'unit' => 'pcs'],
                ['name' => 'Bumbu Petis', 'amount' => 30, 'unit' => 'gram'],
                ['name' => 'Cabai Rawit Hijau', 'amount' => 4, 'unit' => 'pcs'],
            ],
            'Tahu Walik' => [
                ['name' => 'Tahu Walik Ayam', 'amount' => 6, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Sambal Kecap', 'amount' => 25, 'unit' => 'ml'],
            ],
            'Cireng Salju + Saus Bangkok' => [
                ['name' => 'Adonan Cireng', 'amount' => 10, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Saus Bangkok', 'amount' => 30, 'unit' => 'ml'],
            ],
            'Risol Original' => [
                ['name' => 'Risol Frozen', 'amount' => 3, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Cabai Rawit', 'amount' => 3, 'unit' => 'pcs'],
            ],
            'Risol Mayones' => [
                ['name' => 'Risol Mayo Frozen', 'amount' => 3, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
            ],
            'Lumpia Sayur' => [
                ['name' => 'Lumpia Frozen', 'amount' => 3, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Saus Acar', 'amount' => 1, 'unit' => 'porsi'],
            ],
            'French Fries' => [
                ['name' => 'Kentang Beku', 'amount' => 150, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Saus Sachet', 'amount' => 2, 'unit' => 'pcs'],
            ],
            'Nugget Ayam' => [
                ['name' => 'Nugget Ayam Frozen', 'amount' => 6, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
            ],
            'Mix Snack' => [
                ['name' => 'Kentang Beku', 'amount' => 80, 'unit' => 'gram'],
                ['name' => 'Nugget Ayam', 'amount' => 3, 'unit' => 'pcs'],
                ['name' => 'Sosis Sapi', 'amount' => 2, 'unit' => 'pcs'],
                ['name' => 'Minyak Goreng', 'amount' => 50, 'unit' => 'ml'],
            ],

            // ==========================================
            // 3. FOOD
            // ==========================================
            'Nasi Goreng Jawa' => [
                ['name' => 'Beras / Nasi Putih', 'amount' => 200, 'unit' => 'gram'],
                ['name' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                ['name' => 'Bumbu Nasi Goreng', 'amount' => 30, 'unit' => 'gram'],
                ['name' => 'Daging Ayam Suwir', 'amount' => 30, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 20, 'unit' => 'ml'],
            ],
            'Nasi Goreng Seafood' => [
                ['name' => 'Beras / Nasi Putih', 'amount' => 200, 'unit' => 'gram'],
                ['name' => 'Udang & Cumi', 'amount' => 50, 'unit' => 'gram'],
                ['name' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                ['name' => 'Bumbu Nasi Goreng', 'amount' => 30, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 20, 'unit' => 'ml'],
            ],
            'Mie Nyemek' => [
                ['name' => 'Mie Telur', 'amount' => 1, 'unit' => 'keping'],
                ['name' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                ['name' => 'Sayuran Kol/Sawi', 'amount' => 40, 'unit' => 'gram'],
                ['name' => 'Bumbu Racik', 'amount' => 25, 'unit' => 'gram'],
            ],
            'Mie Goreng' => [
                ['name' => 'Mie Telur', 'amount' => 1, 'unit' => 'keping'],
                ['name' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                ['name' => 'Sayuran Kol/Sawi', 'amount' => 40, 'unit' => 'gram'],
                ['name' => 'Bumbu Racik', 'amount' => 25, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 20, 'unit' => 'ml'],
            ],
            'Kwetiau' => [
                ['name' => 'Kwetiau Basah', 'amount' => 180, 'unit' => 'gram'],
                ['name' => 'Telur Ayam', 'amount' => 1, 'unit' => 'butir'],
                ['name' => 'Sosis / Ayam', 'amount' => 30, 'unit' => 'gram'],
                ['name' => 'Sayuran & Bumbu', 'amount' => 35, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 20, 'unit' => 'ml'],
            ],
            'Chicken Blackpaper' => [
                ['name' => 'Beras / Nasi Putih', 'amount' => 200, 'unit' => 'gram'],
                ['name' => 'Daging Ayam Fillet', 'amount' => 100, 'unit' => 'gram'],
                ['name' => 'Saus Lada Hitam', 'amount' => 40, 'unit' => 'gram'],
                ['name' => 'Bawang Bombay', 'amount' => 30, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 20, 'unit' => 'ml'],
            ],
            'Ayam Chili Padi' => [
                ['name' => 'Beras / Nasi Putih', 'amount' => 200, 'unit' => 'gram'],
                ['name' => 'Daging Ayam Fillet', 'amount' => 100, 'unit' => 'gram'],
                ['name' => 'Cabai Rawit Merah & Hijau', 'amount' => 35, 'unit' => 'gram'],
                ['name' => 'Minyak Goreng', 'amount' => 20, 'unit' => 'ml'],
            ],

            // ==========================================
            // 4. CLASSIC COFFEE
            // ==========================================
            'Americano' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Long Black' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Cappuccino' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Caffe Latte' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Magic' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Hazelnut' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Flavour Terkait', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Caramel' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Flavour Terkait', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Vanilla' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Flavour Terkait', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Butterscotch' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Flavour Terkait', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Gula Aren' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Flavour Terkait', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Moccacino' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Flavour Terkait', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Kopi Susu Berco' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Susu Racikan Khas Berco', 'amount' => 130, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Lemonade Americano' => [
                ['name' => 'Biji Kopi House Blend', 'amount' => 18, 'unit' => 'gram'],
                ['name' => 'Sirup Lemon', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Soda', 'amount' => 100, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],

            // ==========================================
            // 5. NON COFFEE
            // ==========================================
            'Summer Strawberry' => [
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Summer Lychee' => [
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Summer Orange' => [
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Sparkling Strawberry' => [
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Soda', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Sparkling Lychee' => [
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Soda', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Sparkling Orange' => [
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Soda', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Lychee Tea' => [
                ['name' => 'Biang Teh Hitam', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Lemon Tea' => [
                ['name' => 'Biang Teh Hitam', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Berry Tea' => [
                ['name' => 'Biang Teh Hitam', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Chocolate Milk' => [
                ['name' => 'Bubuk Perisa Pilihan', 'amount' => 25, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Taro Milk' => [
                ['name' => 'Bubuk Perisa Pilihan', 'amount' => 25, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Green Tea Milk' => [
                ['name' => 'Bubuk Perisa Pilihan', 'amount' => 25, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Matcha Latte' => [
                ['name' => 'Pure Matcha Powder', 'amount' => 15, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Matcha Strawberry' => [
                ['name' => 'Pure Matcha Powder', 'amount' => 15, 'unit' => 'gram'],
                ['name' => 'Susu Fresh Milk', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 20, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Matcha Orange' => [
                ['name' => 'Pure Matcha Powder', 'amount' => 15, 'unit' => 'gram'],
                ['name' => 'Sirup Konsentrat Buah', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 100, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Matchacano' => [
                ['name' => 'Pure Matcha Powder', 'amount' => 15, 'unit' => 'gram'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Jahe Hangat' => [
                ['name' => 'Biang Jahe Rempah', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Susu Jahe' => [
                ['name' => 'Biang Jahe Rempah', 'amount' => 50, 'unit' => 'ml'],
                ['name' => 'Susu Kental Manis', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Jhosua' => [
                ['name' => 'Extra Joss Sachet', 'amount' => 1, 'unit' => 'pcs'],
                ['name' => 'Susu Kental Manis', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Mineral', 'amount' => 150, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
            'Sogem' => [
                ['name' => 'Sirup Cocopandan', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Susu Kental Manis', 'amount' => 30, 'unit' => 'ml'],
                ['name' => 'Air Soda', 'amount' => 120, 'unit' => 'ml'],
                ['name' => 'Cup (Paper/Plastic)', 'amount' => 1, 'unit' => 'pcs'],
            ],
        ];

        // 4. ATTACH RECIPES TO PRODUCTS AND CALCULATE TOTAL HPP
        $seededCount = 0;
        foreach ($recipesData as $menuName => $items) {
            $product = Menu::where('nama_menu', $menuName)->first()
                    ?? Product::where('name', $menuName)->first();

            if (! $product) {
                continue;
            }

            // Clean previous recipes for this product
            ProductRecipe::where('menu_id', $product->id_menu)->delete();

            $calculatedHpp = 0;

            foreach ($items as $ingItem) {
                $ingredient = Ingredient::where('name', $ingItem['name'])->first()
                           ?? RawMaterial::where('name', $ingItem['name'])->first();

                if ($ingredient) {
                    ProductRecipe::create([
                        'menu_id' => $product->id_menu,
                        'raw_material_id' => $ingredient->id,
                        'quantity_used' => $ingItem['amount'],
                    ]);

                    $cost = (float) $ingItem['amount'] * (float) $ingredient->purchase_price;
                    $calculatedHpp += $cost;
                }
            }

            // Update product HPP automatically
            if ($calculatedHpp > 0) {
                $product->hpp = round($calculatedHpp, 2);
                $product->save();
                $seededCount++;
            }
        }

        $this->command?->info("Berhasil menghubungkan resep dan kalkulasi HPP otomatis untuk {$seededCount} produk menu.");
    }
}
