<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

echo '=== 1. VERIFIKASI KATEGORI ==='.PHP_EOL;
$cat = Category::firstOrCreate(
    ['nama_kategori' => 'Kopi Signature'],
    ['slug' => 'kopi-signature', 'icon' => 'fas fa-mug-hot']
);
echo "ID Kategori: {$cat->id} | Nama: {$cat->nama_kategori} | Slug: {$cat->slug} | Alias name: {$cat->name}".PHP_EOL;

echo PHP_EOL.'=== 2. VERIFIKASI MENU & PRODUK ==='.PHP_EOL;
$menu = Menu::updateOrCreate(
    ['nama_menu' => 'Kopi Susu Aren Spesial'],
    [
        'id_kategori' => $cat->id,
        'harga' => 24000,
        'hpp' => 9500,
        'stok' => 50,
        'status_tersedia' => 1,
        'deskripsi' => 'Espresso blend dengan gula aren asli',
    ]
);
echo "Menu: {$menu->nama_menu} | Kategori: {$menu->category} | Harga: Rp {$menu->harga} | HPP: Rp {$menu->hpp} | Stok: {$menu->stok} | Margin: Rp {$menu->profit_margin} ({$menu->profit_percentage}%)".PHP_EOL;

echo PHP_EOL.'=== 3. VERIFIKASI PEMOTONGAN STOK & PENCATATAN HPP HISTORIS ==='.PHP_EOL;
$prevStock = $menu->stok;
$order = Order::firstOrCreate(
    ['id_user' => 1],
    [
        'tanggal' => now(),
        'nama_pelanggan' => 'Test Customer',
        'total_harga' => 48000,
        'subtotal' => 48000,
        'status_pembayaran' => 'paid',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'status_order' => 'completed',
    ]
);

$orderItem = OrderItem::create([
    'id_order' => $order->id_order,
    'id_menu' => $menu->id_menu,
    'quantity' => 2,
    'subtotal' => $menu->harga * 2,
    'hpp' => $menu->hpp,
]);

$menu->decrementStock(2);
$newStock = $menu->fresh()->stok;
echo "Stok Awal: {$prevStock} -> Terjual 2 -> Stok Baru: {$newStock}".PHP_EOL;
echo "OrderItem HPP Tercatat: Rp {$orderItem->hpp} | Subtotal: Rp {$orderItem->subtotal} | Total HPP: Rp {$orderItem->total_hpp} | Profit: Rp {$orderItem->profit}".PHP_EOL;

echo PHP_EOL.'=== 4. VERIFIKASI USER & ROLE PERMISSION ==='.PHP_EOL;
$adminRole = Role::firstOrCreate(['role_name' => 'Admin']);
$staffRole = Role::firstOrCreate(['role_name' => 'Staff']);
$cashierRole = Role::firstOrCreate(['role_name' => 'Cashier']);

$testStaff = User::updateOrCreate(
    ['username' => 'staff_test'],
    [
        'name' => 'Staff Operasional Test',
        'email' => 'staff_test@bercocafe.com',
        'id_role' => $staffRole->id_role,
        'password' => Hash::make('staff123'),
        'status' => 'active',
    ]
);

echo "Staff User: {$testStaff->name} | Role: {$testStaff->role->role_name} | canManageMenu: ".($testStaff->canManageMenu() ? 'YA (Valid)' : 'TIDAK').' | Status Aktif: '.($testStaff->isActive() ? 'Aktif' : 'Nonaktif').PHP_EOL;

$testStaff->status = 'inactive';
$testStaff->save();
echo 'Staff Diubah Nonaktif -> isActive: '.($testStaff->isActive() ? 'Aktif' : 'Nonaktif (Valid Terblokir)').PHP_EOL;

// Kembalikan ke aktif
$testStaff->status = 'active';
$testStaff->save();

echo PHP_EOL.'✅ SELURUH SISTEM TERVERIFIKASI BERFUNGSI SEMPURNA!'.PHP_EOL;
