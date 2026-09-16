<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Menu;

$samples = [
    'Pisang Chocolate Original',
    'Churros Original',
    'Donut Berco Original',
    'Sosis Bakar',
    'Tahu Petis',
    'Cireng Salju + Saus Bangkok',
    'Nasi Goreng Jawa',
    'Chicken Blackpaper',
    'Americano',
    'Cappuccino',
    'Kopi Susu Hazelnut',
    'Matcha Latte',
    'Lychee Tea',
];

echo "====================================================\n";
echo "  VERIFIKASI RESEP & HPP MENU CAFE BERCO\n";
echo "====================================================\n";

foreach ($samples as $name) {
    $m = Menu::where('nama_menu', $name)->with('recipes.rawMaterial')->first();
    if (! $m) {
        echo "❌ Not found: {$name}\n";

        continue;
    }

    $profit = max(0, $m->harga - $m->hpp);
    $margin = $m->harga > 0 ? round(($profit / $m->harga) * 100) : 0;

    echo sprintf("• %-28s | Harga: Rp %-6s | HPP: Rp %-6s | Margin: %d%%\n",
        $m->nama_menu,
        number_format($m->harga, 0, ',', '.'),
        number_format($m->hpp, 0, ',', '.'),
        $margin
    );

    foreach ($m->recipes as $r) {
        $cost = (float) $r->quantity_used * (float) ($r->rawMaterial?->purchase_price ?? 0);
        echo sprintf("    ↳ %-25s : %-3s %-6s (@ Rp %-4s = Rp %s)\n",
            $r->rawMaterial?->name,
            (float) $r->quantity_used,
            $r->rawMaterial?->unit,
            number_format($r->rawMaterial?->purchase_price ?? 0, 0, ',', '.'),
            number_format($cost, 0, ',', '.')
        );
    }
    echo "\n";
}
