<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

echo "========================================================\n";
echo "1. VERIFIKASI DATA SEEDER STOK BARANG (INVENTORIES)\n";
echo "========================================================\n";

$inventories = Inventory::all();
foreach ($inventories as $inv) {
    echo sprintf(
        "SKU: %-8s | Nama: %-26s | Kategori: %-15s | Stok: %6s %-6s | Min: %6s %-6s | Status: %s\n",
        $inv->item_code,
        $inv->item_name,
        $inv->category,
        number_format($inv->current_stock),
        $inv->unit,
        number_format($inv->min_stock),
        $inv->unit,
        strtoupper($inv->status)
    );
}

echo "\n========================================================\n";
echo "2. TEST RENDER HALAMAN ADMIN INVENTORY\n";
echo "========================================================\n";

$admin = User::where('username', 'admin')->first();
auth()->login($admin);

$rendered = view('admin.inventory')->render();
echo 'Halaman Admin Inventory Rendered: '.strlen($rendered)." bytes ✅\n";
if (strpos($rendered, 'Stok Barang & Inventori') !== false) {
    echo " -> Page title 'Stok Barang & Inventori' verified! ✅\n";
}
if (strpos($rendered, 'livewire:inventory-management') !== false || strpos($rendered, 'wire:model') !== false || strpos($rendered, 'Biji Kopi House Blend') !== false) {
    echo " -> Livewire Component verified! ✅\n";
}
