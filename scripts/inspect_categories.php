<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\Menu;

echo "=== CATEGORIES IN DATABASE ===\n";
foreach (Category::withCount('menus')->get() as $cat) {
    echo "ID: {$cat->id} | {$cat->nama_kategori} (Slug: {$cat->slug}) | Total Menus: {$cat->menus_count}\n";
    foreach ($cat->menus as $m) {
        echo "   -> [{$m->id_menu}] {$m->nama_menu} (Rp ".number_format($m->harga).")\n";
    }
}

$orphanMenus = Menu::whereNull('id_kategori')->orWhereNotIn('id_kategori', Category::pluck('id'))->get();
if ($orphanMenus->count() > 0) {
    echo "\n=== ORPHAN MENUS (No valid category) ===\n";
    foreach ($orphanMenus as $m) {
        echo "   -> [{$m->id_menu}] {$m->nama_menu} (id_kategori: {$m->id_kategori})\n";
    }
}
