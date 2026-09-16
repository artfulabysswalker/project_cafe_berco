<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Menu;

$expectedProducts = [
    'Black Coffee',
    'White Coffee',
    'Kopi Susu Flavored',
    'Kopi Susu Berco (Signature)',
    'Lemonade Americano (Signature)',
    'Summer',
    'Sparkling Drink',
    'Tea',
    'Milkbase',
    'Matcha Series',
    'Lokal Pride',
    'Donut Berco',
    'Risol',
    'Cireng Salju',
    'French Fries',
    'Mix Snack',
    'Nasi Goreng Jawa',
    'Mie Nyemek',
    'Ayam Chili Padi',
];

echo "================================================================================\n";
echo "                   VERIFIKASI PRODUK MENU CAFE BERCO\n";
echo "================================================================================\n";

$foundCount = 0;
foreach ($expectedProducts as $idx => $name) {
    $m = Menu::with(['categoryRelation', 'recipes.rawMaterial'])->where('nama_menu', $name)->first();
    $num = $idx + 1;
    if ($m) {
        $foundCount++;
        echo "[$num/19] ✓ {$m->nama_menu}\n";
        echo "       Kategori        : {$m->categoryRelation?->nama_kategori}\n";
        echo '       Harga Base / HPP: Rp '.number_format($m->harga, 0, ',', '.').' / Rp '.number_format($m->hpp, 0, ',', '.')."\n";
        if (! empty($m->sub_variants)) {
            echo '       Sub Varian      : '.json_encode($m->sub_variants, JSON_UNESCAPED_UNICODE)."\n";
        }
        if ($m->has_temperature_option || ! empty($m->temperature_options)) {
            echo '       Opsi Suhu (Hot/Cold) : '.($m->has_temperature_option ? 'Ya' : 'Hanya Cold/Tetap').' -> '.json_encode($m->temperature_options)."\n";
        }
        if (! empty($m->flavor_options)) {
            echo '       Pilihan Rasa    : '.json_encode($m->flavor_options, JSON_UNESCAPED_UNICODE)."\n";
        }
        if (! empty($m->variant_selection_rules)) {
            echo '       Selection Rules : '.json_encode($m->variant_selection_rules, JSON_UNESCAPED_UNICODE)."\n";
        }
        if ($m->portion_count > 1) {
            echo "       Porsi           : {$m->portion_count} pcs\n";
        }
        echo '       Resep Bahan     : '.$m->recipes->map(fn ($r) => ($r->rawMaterial?->name ?? '?').' ('.(float) $r->quantity_used.' '.($r->rawMaterial?->unit ?? '').')')->implode(', ')."\n";
        echo "--------------------------------------------------------------------------------\n";
    } else {
        echo "[$num/19] ✗ TIDAK DITEMUKAN: $name\n";
        echo "--------------------------------------------------------------------------------\n";
    }
}

echo "\nTOTAL TERVERIFIKASI: $foundCount / ".count($expectedProducts)." PRODUK\n";
