<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Menu;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;

echo "=== SEEDING RAW MATERIALS & PRODUCT RECIPES ===\n";

// 1. Raw Materials Database
$materials = [
    // Coffee Beans
    'bean_house' => ['name' => 'Biji Kopi House Blend (Arabica/Robusta)', 'unit' => 'gr', 'purchase_price' => 140.00, 'stock_quantity' => 20000, 'notes' => 'Rp 140.000/kg'],
    'bean_ijen' => ['name' => 'Biji Kopi Single Origin Ijen Honey', 'unit' => 'gr', 'purchase_price' => 180.00, 'stock_quantity' => 15000, 'notes' => 'Rp 180.000/kg'],
    'bean_gayo' => ['name' => 'Biji Kopi Single Origin Gayo Wine', 'unit' => 'gr', 'purchase_price' => 220.00, 'stock_quantity' => 10000, 'notes' => 'Rp 220.000/kg'],
    'bean_geisha' => ['name' => 'Biji Kopi Panama Geisha Reserve', 'unit' => 'gr', 'purchase_price' => 500.00, 'stock_quantity' => 3000, 'notes' => 'Rp 500.000/kg micro-lot'],

    // Dairy & Plant-based Milks
    'milk_fresh' => ['name' => 'Susu Fresh Milk (Greenfields)', 'unit' => 'ml', 'purchase_price' => 22.00, 'stock_quantity' => 35000, 'notes' => 'Rp 22.000/Liter'],
    'milk_oat' => ['name' => 'Oat Milk Barista Edition (Oatside)', 'unit' => 'ml', 'purchase_price' => 42.00, 'stock_quantity' => 15000, 'notes' => 'Rp 42.000/Liter'],
    'condensed' => ['name' => 'Susu Kental Manis Carnation', 'unit' => 'ml', 'purchase_price' => 28.00, 'stock_quantity' => 10000, 'notes' => 'Rp 28.000/kg'],

    // Syrups & Sugars
    'sugar_aren' => ['name' => 'Gula Aren Cair Organik Banyuwangi', 'unit' => 'ml', 'purchase_price' => 35.00, 'stock_quantity' => 12000, 'notes' => 'Rp 35.000/Liter murni'],
    'syrup_caramel' => ['name' => 'Sirup Karamel Gold (Monin)', 'unit' => 'ml', 'purchase_price' => 185.00, 'stock_quantity' => 3500, 'notes' => 'Rp 130.000/700ml'],
    'syrup_vanilla' => ['name' => 'Sirup Vanilla Madagascar (Monin)', 'unit' => 'ml', 'purchase_price' => 185.00, 'stock_quantity' => 3500, 'notes' => 'Rp 130.000/700ml'],
    'syrup_hazelnut' => ['name' => 'Sirup Hazelnut Roast (Monin)', 'unit' => 'ml', 'purchase_price' => 185.00, 'stock_quantity' => 2800, 'notes' => 'Rp 130.000/700ml'],

    // Powders & Teas
    'powder_matcha' => ['name' => 'Matcha Powder Ceremonial Uji', 'unit' => 'gr', 'purchase_price' => 650.00, 'stock_quantity' => 2000, 'notes' => 'Rp 650/gr grade A'],
    'powder_choco' => ['name' => 'Pure Dark Cacao Powder 70%', 'unit' => 'gr', 'purchase_price' => 160.00, 'stock_quantity' => 4000, 'notes' => 'Rp 160/gr'],
    'powder_taro' => ['name' => 'Taro Premium Flavour Powder', 'unit' => 'gr', 'purchase_price' => 120.00, 'stock_quantity' => 3000, 'notes' => 'Rp 120/gr'],
    'tea_earlgrey' => ['name' => 'Loose Leaf Earl Grey Tea', 'unit' => 'gr', 'purchase_price' => 250.00, 'stock_quantity' => 1500, 'notes' => 'Rp 250/gr'],

    // Packaging & Disposables
    'cup_cold' => ['name' => 'Cup + Dome Lid + Straw 14oz', 'unit' => 'pcs', 'purchase_price' => 1250.00, 'stock_quantity' => 2500, 'notes' => 'Biodegradable cold cup'],
    'cup_hot' => ['name' => 'Paper Cup Double Wall + Lid 8oz', 'unit' => 'pcs', 'purchase_price' => 950.00, 'stock_quantity' => 1800, 'notes' => 'Hot beverage cup'],
    'paper_bag' => ['name' => 'Kraft Paper Bag Takeaway', 'unit' => 'pcs', 'purchase_price' => 1500.00, 'stock_quantity' => 900, 'notes' => 'Food takeaway bag'],

    // Bakery, Food & Ingredients
    'dough_croissant' => ['name' => 'Frozen Croissant Dough (Isigny Butter)', 'unit' => 'pcs', 'purchase_price' => 8500.00, 'stock_quantity' => 200, 'notes' => 'French imported dough'],
    'beef_slice' => ['name' => 'US Shortplate Beef Slice', 'unit' => 'gr', 'purchase_price' => 145.00, 'stock_quantity' => 8000, 'notes' => 'Rp 145.000/kg'],
    'sauce_teriyaki' => ['name' => 'Authentic Teriyaki Sauce', 'unit' => 'ml', 'purchase_price' => 65.00, 'stock_quantity' => 4000, 'notes' => 'Rp 65.000/Liter'],
    'rice_portion' => ['name' => 'Beras Organik Pulen (Porsi)', 'unit' => 'porsi', 'purchase_price' => 2500.00, 'stock_quantity' => 300, 'notes' => 'Per 150gr nasi matang'],
    'egg_fresh' => ['name' => 'Telur Ayam Omega 3', 'unit' => 'pcs', 'purchase_price' => 2400.00, 'stock_quantity' => 250, 'notes' => 'Per butir'],
];

$savedMaterials = [];
foreach ($materials as $key => $data) {
    $mat = RawMaterial::firstOrCreate(
        ['name' => $data['name']],
        $data
    );
    $savedMaterials[$key] = $mat;
}

echo 'Created/Verified '.count($savedMaterials)." raw materials.\n";

// 2. Map existing menus and connect realistic recipes
$allMenus = Menu::all();
echo 'Found '.$allMenus->count()." menus in database.\n";

foreach ($allMenus as $menu) {
    $name = strtolower($menu->nama_menu);
    $menu->recipes()->delete(); // reset to fresh recipes

    // Helper to add ingredient
    $add = function ($matKey, $qty) use ($menu, $savedMaterials) {
        if (isset($savedMaterials[$matKey])) {
            ProductRecipe::create([
                'menu_id' => $menu->id_menu,
                'raw_material_id' => $savedMaterials[$matKey]->id,
                'quantity_used' => $qty,
            ]);
        }
    };

    if (str_contains($name, 'gula aren') || str_contains($name, 'kopi susu')) {
        // Espresso + Fresh Milk + Aren + Cup
        $add('bean_house', 18);     // 18gr coffee = Rp 2.520
        $add('milk_fresh', 110);    // 110ml milk = Rp 2.420
        $add('sugar_aren', 25);     // 25ml aren = Rp 875
        $add('cup_cold', 1);        // 1 cup = Rp 1.250
        // Total HPP ~ Rp 7.065
    } elseif (str_contains($name, 'caramel') || str_contains($name, 'macchiato')) {
        $add('bean_house', 18);
        $add('milk_fresh', 130);
        $add('syrup_caramel', 20);
        $add('cup_cold', 1);
        // Total HPP ~ Rp 10.330
    } elseif (str_contains($name, 'vanilla') || str_contains($name, 'hazelnut')) {
        $add('bean_house', 18);
        $add('milk_fresh', 130);
        $add('syrup_vanilla', 20);
        $add('cup_cold', 1);
    } elseif (str_contains($name, 'cortado')) {
        $add('bean_house', 18);
        $add('milk_fresh', 60);
        $add('cup_hot', 1);
        // Total HPP ~ Rp 4.790
    } elseif (str_contains($name, 'flat white') || str_contains($name, 'latte') || str_contains($name, 'cappuccino')) {
        $add('bean_house', 18);
        $add('milk_fresh', 150);
        $add('cup_hot', 1);
        // Total HPP ~ Rp 6.770
    } elseif (str_contains($name, 'americano') || str_contains($name, 'espresso') || str_contains($name, 'long black')) {
        $add('bean_house', 18);
        $add('cup_cold', 1);
        // Total HPP ~ Rp 3.770
    } elseif (str_contains($name, 'geisha')) {
        $add('bean_geisha', 15);
        $add('cup_hot', 1);
        // Total HPP ~ Rp 8.450
    } elseif (str_contains($name, 'v60') || str_contains($name, 'manual brew') || str_contains($name, 'filter') || str_contains($name, 'gayo') || str_contains($name, 'ijen')) {
        $add('bean_ijen', 15);
        $add('cup_hot', 1);
        // Total HPP ~ Rp 3.650
    } elseif (str_contains($name, 'matcha')) {
        $add('powder_matcha', 8);   // 8gr = Rp 5.200
        $add('milk_fresh', 140);    // 140ml = Rp 3.080
        $add('sugar_aren', 15);     // 15ml = Rp 525
        $add('cup_cold', 1);        // Rp 1.250
        // Total HPP ~ Rp 10.055
    } elseif (str_contains($name, 'chocolate') || str_contains($name, 'cokelat') || str_contains($name, 'choco')) {
        $add('powder_choco', 25);
        $add('milk_fresh', 130);
        $add('cup_cold', 1);
    } elseif (str_contains($name, 'taro')) {
        $add('powder_taro', 25);
        $add('milk_fresh', 130);
        $add('cup_cold', 1);
    } elseif (str_contains($name, 'croissant') || str_contains($name, 'pastry') || str_contains($name, 'kouign')) {
        $add('dough_croissant', 1);
        $add('paper_bag', 1);
        // Total HPP ~ Rp 10.000
    } elseif (str_contains($name, 'rice bowl') || str_contains($name, 'teriyaki') || str_contains($name, 'beef') || str_contains($name, 'nasi')) {
        $add('beef_slice', 90);      // 90gr beef = Rp 13.050
        $add('sauce_teriyaki', 30);  // 30ml = Rp 1.950
        $add('rice_portion', 1);     // Rp 2.500
        $add('egg_fresh', 1);        // Rp 2.400
        $add('paper_bag', 1);        // Rp 1.500
        // Total HPP ~ Rp 21.400
    } else {
        // General default recipe
        $add('bean_house', 18);
        $add('cup_cold', 1);
    }

    $menu->updateHppFromRecipe();
    echo "✓ Linked recipe for [{$menu->nama_menu}]: Selling Price = Rp ".number_format($menu->harga, 0, ',', '.').' | HPP = Rp '.number_format($menu->hpp, 0, ',', '.').' | Margin = '.$menu->profit_percentage."%\n";
}

echo "=== SEEDING COMPLETED SUCCESSFULLY ===\n";
