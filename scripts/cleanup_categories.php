<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\Menu;
use App\Models\ProductRecipe;
use Illuminate\Support\Facades\DB;

echo "=== CLEANING UP OBSOLETE CATEGORIES & DUMMY MENUS ===\n";

// 1. Standard 5 categories
$validCategories = [
    'Classic Coffee' => ['slug' => 'classic-coffee', 'icon' => 'fas fa-coffee'],
    'Non Coffee' => ['slug' => 'non-coffee', 'icon' => 'fas fa-mug-hot'],
    'Food' => ['slug' => 'food', 'icon' => 'fas fa-utensils'],
    'Snack' => ['slug' => 'snack', 'icon' => 'fas fa-cookie-bite'],
    'Dessert' => ['slug' => 'dessert', 'icon' => 'fas fa-cake-candles'],
];

// Ensure valid categories exist
$validCategoryIds = [];
foreach ($validCategories as $name => $meta) {
    $cat = Category::firstOrCreate(['nama_kategori' => $name], $meta);
    $validCategoryIds[$name] = $cat->id;
}

// 2. Map of 19 official Cafe Berco menus to their respective category
$officialMenuCategoryMap = [
    'Black Coffee' => 'Classic Coffee',
    'White Coffee' => 'Classic Coffee',
    'Kopi Susu Flavored' => 'Classic Coffee',
    'Kopi Susu Berco (Signature)' => 'Classic Coffee',
    'Lemonade Americano (Signature)' => 'Classic Coffee',
    'Summer' => 'Non Coffee',
    'Sparkling Drink' => 'Non Coffee',
    'Tea' => 'Non Coffee',
    'Milkbase' => 'Non Coffee',
    'Matcha Series' => 'Non Coffee',
    'Lokal Pride' => 'Non Coffee',
    'Donut Berco' => 'Dessert',
    'Risol' => 'Snack',
    'Cireng Salju' => 'Snack',
    'French Fries' => 'Snack',
    'Mix Snack' => 'Snack',
    'Nasi Goreng Jawa' => 'Food',
    'Mie Nyemek' => 'Food',
    'Ayam Chili Padi' => 'Food',
];

// Update official menus to the correct category ID
foreach ($officialMenuCategoryMap as $menuName => $catName) {
    $menu = Menu::where('nama_menu', $menuName)->first();
    if ($menu && isset($validCategoryIds[$catName])) {
        $menu->update(['id_kategori' => $validCategoryIds[$catName]]);
    }
}

// Remove old dummy menus that are NOT in the official list and have no valid foreign relations
$allMenus = Menu::all();
foreach ($allMenus as $m) {
    if (! isset($officialMenuCategoryMap[$m->nama_menu])) {
        echo "Removing obsolete menu: [{$m->id_menu}] {$m->nama_menu}\n";
        // Check if referenced in order_items
        $orderItemCount = DB::table('order_items')->where('id_menu', $m->id_menu)->count();
        if ($orderItemCount == 0) {
            ProductRecipe::where('menu_id', $m->id_menu)->delete();
            $m->delete();
        } else {
            // Re-assign to Classic Coffee or Snack if ordered in test
            $m->update(['id_kategori' => $validCategoryIds['Classic Coffee'], 'status_tersedia' => 0]);
        }
    }
}

// Remove obsolete categories (any category not in the 5 valid categories)
$obsoleteCategories = Category::whereNotIn('id', array_values($validCategoryIds))->get();
foreach ($obsoleteCategories as $obCat) {
    echo "Deleting obsolete category: [{$obCat->id}] {$obCat->nama_kategori}\n";
    // Check if menus still linked
    Menu::where('id_kategori', $obCat->id)->update(['id_kategori' => $validCategoryIds['Classic Coffee']]);
    $obCat->delete();
}

echo "\n=== CLEANUP FINISHED ===\n";
echo 'Active Categories: '.Category::count()."\n";
foreach (Category::withCount('menus')->get() as $cat) {
    echo " - [{$cat->id}] {$cat->nama_kategori} (Slug: {$cat->slug}) | Total Menus: {$cat->menus_count}\n";
}
echo 'Total Active Menus: '.Menu::count()."\n";
