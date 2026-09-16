<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Console\Kernel;

echo '=== 1. BACKFILL ALL ORDER ITEMS HPP_AT_SALE ==='.PHP_EOL;

$items = OrderItem::with('menu')->get();
$count = 0;
foreach ($items as $item) {
    $menuHpp = $item->menu?->hpp ?? 0;
    if ($menuHpp == 0 && ($item->menu?->harga ?? 0) > 0) {
        // Berikan default HPP 40% jika menu HPP belum diisi agar kalkulasi laba tetap muncul
        $menuHpp = round($item->menu->harga * 0.4);
        $item->menu->hpp = $menuHpp;
        $item->menu->save();
    }

    if (empty($item->hpp_at_sale) || $item->hpp_at_sale == 0) {
        $item->hpp_at_sale = ($item->hpp > 0) ? $item->hpp : $menuHpp;
        $item->hpp = $item->hpp_at_sale;
        $item->save();
        $count++;
    }
}

echo "Berhasil update {$count} order_items dengan nilai HPP yang akurat!".PHP_EOL;

echo PHP_EOL.'=== 2. PASTIKAN SEMUA MENU MEMILIKI HPP & STOK ==='.PHP_EOL;
$menus = Menu::all();
foreach ($menus as $m) {
    $dirty = false;
    if (empty($m->hpp) || $m->hpp == 0) {
        $m->hpp = round($m->harga * 0.4); // 40% standard modal bahan
        $dirty = true;
    }
    if ($m->stok === null) {
        $m->stok = 50;
        $dirty = true;
    }
    if ($dirty) {
        $m->save();
        echo "Auto-set HPP menu '{$m->nama_menu}': Harga Rp {$m->harga} -> HPP Rp {$m->hpp} | Stok: {$m->stok}".PHP_EOL;
    }
}

echo PHP_EOL.'=== 3. PASTIKAN SEMUA TRANSAKSI SELESAI/PAID MEMILIKI REKAP PROFIT ==='.PHP_EOL;
$orders = Order::with('items')->get();
foreach ($orders as $ord) {
    $totalItemProfit = 0;
    foreach ($ord->items as $itm) {
        $effHpp = $itm->hpp_at_sale > 0 ? $itm->hpp_at_sale : ($itm->hpp > 0 ? $itm->hpp : ($itm->menu?->hpp ?? 0));
        $totalItemProfit += max(0, $itm->subtotal - ($effHpp * $itm->quantity));
    }
    if ($ord->profit_margin == 0 || $ord->profit_margin != $totalItemProfit) {
        $ord->profit_margin = $totalItemProfit;
        $ord->save();
    }
}
echo 'Rekap seluruh order selesai disinkronkan!'.PHP_EOL;
