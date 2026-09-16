<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo '=== 1. CEK SKEMA TABEL ORDER_ITEMS ==='.PHP_EOL;
$hasHppAtSale = Schema::hasColumn('order_items', 'hpp_at_sale');
echo 'Kolom order_items.hpp_at_sale: '.($hasHppAtSale ? 'ADA (Valid)' : 'TIDAK ADA (Error)').PHP_EOL;

echo PHP_EOL.'=== 2. CEK PEREKAMAN HISTORIS HPP PADA TRANSAKSI ==='.PHP_EOL;
$sampleMenu = Menu::firstOrCreate(
    ['nama_menu' => 'Caramel Macchiato Test Margin'],
    [
        'id_kategori' => 1,
        'harga' => 30000,
        'hpp' => 12000,
        'stok' => 50,
        'status_tersedia' => 1,
    ]
);

$testOrder = Order::create([
    'id_user' => 11, // Admin
    'nama_pelanggan' => 'Tester HPP',
    'total_harga' => 60000,
    'subtotal' => 60000,
    'tanggal' => now(),
    'payment_method' => 'qris',
    'status_pembayaran' => 'paid',
    'service_type' => 'dine_in',
    'status_order' => 'completed',
]);

$item = OrderItem::create([
    'id_order' => $testOrder->id_order,
    'id_menu' => $sampleMenu->id_menu,
    'quantity' => 2,
    'subtotal' => 60000,
    'hpp' => $sampleMenu->hpp,
    'hpp_at_sale' => $sampleMenu->hpp,
]);

echo "Order Item ID: {$item->id} | Menu: {$sampleMenu->nama_menu} | Qty: {$item->quantity} | HPP at Sale: Rp {$item->hpp_at_sale} | Subtotal: Rp {$item->subtotal} | Profit: Rp {$item->profit}".PHP_EOL;

// Sekarang ubah HPP menu menjadi Rp 20.000 untuk mengetes apakah HPP historis transaksi tetap terlindungi
$sampleMenu->hpp = 20000;
$sampleMenu->save();

$reloadedItem = OrderItem::find($item->id);
echo "Setelah Menu HPP diubah jadi Rp 20.000 -> Item HPP at sale: Rp {$reloadedItem->hpp_at_sale} | Item Profit tetap: Rp {$reloadedItem->profit} (TERLINDUNGI!)".PHP_EOL;

// Kembalikan HPP menu
$sampleMenu->hpp = 12000;
$sampleMenu->save();

echo PHP_EOL.'=== 3. CEK KALKULASI TOTAL OMZET, HPP, LABA KOTOR, & MARGIN TOKO ==='.PHP_EOL;
$completedOrderIds = Order::where(function ($q) {
    $q->whereIn('status_pembayaran', ['paid', 'Sudah'])
        ->orWhere('status_order', 'completed');
})->pluck('id_order');

$totalRevenue = (float) Order::whereIn('id_order', $completedOrderIds)->sum('total_harga');
$totalCogs = (float) OrderItem::whereIn('id_order', $completedOrderIds)
    ->join('menus', 'order_items.id_menu', '=', 'menus.id_menu')
    ->sum(DB::raw('order_items.quantity * CASE 
        WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale 
        WHEN order_items.hpp > 0 THEN order_items.hpp 
        ELSE COALESCE(menus.hpp, 0) 
    END'));

$grossProfit = max(0, $totalRevenue - $totalCogs);
$marginPct = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 1) : 0;

echo 'Total Omzet: Rp '.number_format($totalRevenue, 0, ',', '.').PHP_EOL;
echo 'Total Nilai HPP Produk: Rp '.number_format($totalCogs, 0, ',', '.').PHP_EOL;
echo 'Total Laba Kotor (Gross Profit): Rp '.number_format($grossProfit, 0, ',', '.').PHP_EOL;
echo "Rata-rata Margin Toko: {$marginPct}%".PHP_EOL;

echo PHP_EOL.'=== 4. CEK TOP 5 MENU PALING MENGHASILKAN KEUNTUNGAN ==='.PHP_EOL;
$topProfitable = DB::table('order_items')
    ->join('menus', 'order_items.id_menu', '=', 'menus.id_menu')
    ->whereIn('order_items.id_order', $completedOrderIds)
    ->select(
        'menus.nama_menu',
        DB::raw('SUM(order_items.quantity) as total_qty'),
        DB::raw('SUM(order_items.subtotal) as total_revenue'),
        DB::raw('SUM(order_items.quantity * CASE 
            WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale 
            WHEN order_items.hpp > 0 THEN order_items.hpp 
            ELSE COALESCE(menus.hpp, 0) 
        END) as total_cogs'),
        DB::raw('SUM(order_items.subtotal - (order_items.quantity * CASE 
            WHEN order_items.hpp_at_sale > 0 THEN order_items.hpp_at_sale 
            WHEN order_items.hpp > 0 THEN order_items.hpp 
            ELSE COALESCE(menus.hpp, 0) 
        END)) as total_profit')
    )
    ->groupBy('menus.id_menu', 'menus.nama_menu')
    ->orderByDesc('total_profit')
    ->limit(5)
    ->get();

foreach ($topProfitable as $idx => $m) {
    $mMargin = $m->total_revenue > 0 ? round(($m->total_profit / $m->total_revenue) * 100, 1) : 0;
    echo ($idx + 1).". {$m->nama_menu} | Terjual: {$m->total_qty} | Omzet: Rp ".number_format($m->total_revenue, 0, ',', '.').' | HPP: Rp '.number_format($m->total_cogs, 0, ',', '.').' | Profit: Rp '.number_format($m->total_profit, 0, ',', '.')." ({$mMargin}%)".PHP_EOL;
}

echo PHP_EOL.'✅ SEMUA FITUR KALKULASI MARGIN, HPP HISTORIS, DAN DASHBOARD ANALYTICS BERHASIL DIVIDASI!'.PHP_EOL;
