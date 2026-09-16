<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\CashierShift;
use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Contracts\Console\Kernel;

echo "========================================================\n";
echo "1. VERIFIKASI MULTI-ACTIVE CASHIER SHIFTS\n";
echo "========================================================\n";

$shifts = CashierShift::with('user')->get();
foreach ($shifts as $s) {
    echo sprintf(
        "Shift #%d | Kasir: %-7s | Tipe: %-7s | Status: %-6s | Modal: Rp %8s | Cash: Rp %7s | QRIS: Rp %7s | Kas Akhir: Rp %8s | Selisih: Rp %s\n",
        $s->id_shift,
        $s->user->name,
        $s->shift_type,
        $s->status,
        number_format($s->starting_cash, 0, ',', '.'),
        number_format($s->cash_sales, 0, ',', '.'),
        number_format($s->non_cash_sales, 0, ',', '.'),
        number_format($s->actual_cash ?? 0, 0, ',', '.'),
        number_format($s->difference ?? 0, 0, ',', '.')
    );
}

echo "\n========================================================\n";
echo "2. VERIFIKASI ISOLASI DATA TRANSAKSI (NIKITA VS DERY VS ROBIN)\n";
echo "========================================================\n";

$admin = User::where('username', 'admin')->first();
$robin = User::where('username', 'robin')->first();
$nikita = User::where('username', 'nikita')->first();
$dery = User::where('username', 'dery')->first();

// Test Kasir Nikita
$nikitaOrders = Order::forAuthorizedUser($nikita)->get();
echo sprintf("Total Order Kasir Nikita : %d order\n", $nikitaOrders->count());
foreach ($nikitaOrders as $ord) {
    echo sprintf("  -> Order #%d | Pelanggan: %-15s | Kasir: %s | Shift ID: %d | Total: Rp %s\n",
        $ord->id_order, $ord->nama_pelanggan, $ord->user->name, $ord->id_shift, number_format($ord->final_total, 0, ',', '.')
    );
}

// Test Kasir Dery
$deryOrders = Order::forAuthorizedUser($dery)->get();
echo sprintf("\nTotal Order Kasir Dery   : %d order\n", $deryOrders->count());
foreach ($deryOrders as $ord) {
    echo sprintf("  -> Order #%d | Pelanggan: %-15s | Kasir: %s | Shift ID: %d | Total: Rp %s\n",
        $ord->id_order, $ord->nama_pelanggan, $ord->user->name, $ord->id_shift, number_format($ord->final_total, 0, ',', '.')
    );
}

// Test Kasir Robin
$robinOrders = Order::forAuthorizedUser($robin)->get();
echo sprintf("\nTotal Order Kasir Robin  : %d order\n", $robinOrders->count());
foreach ($robinOrders as $ord) {
    echo sprintf("  -> Order #%d | Pelanggan: %-15s | Kasir: %s | Shift ID: %d | Total: Rp %s\n",
        $ord->id_order, $ord->nama_pelanggan, $ord->user->name, $ord->id_shift, number_format($ord->final_total, 0, ',', '.')
    );
}

echo "\n========================================================\n";
echo "3. VERIFIKASI VISIBILITAS ADMIN (FILTER PER SHIFT_TYPE)\n";
echo "========================================================\n";

$adminShift1 = Order::forAuthorizedUser($admin, ['shift_type' => 'shift_1'])->get();
echo sprintf("Admin View Shift 1 (Pagi)  : %d order (Total: Rp %s)\n",
    $adminShift1->count(), number_format($adminShift1->sum('final_total'), 0, ',', '.')
);

$adminShift2 = Order::forAuthorizedUser($admin, ['shift_type' => 'shift_2'])->get();
echo sprintf("Admin View Shift 2 (Sore)  : %d order (Gabungan Nikita & Dery - Total: Rp %s)\n",
    $adminShift2->count(), number_format($adminShift2->sum('final_total'), 0, ',', '.')
);

echo "\n========================================================\n";
echo "4. VERIFIKASI POLICY AUTHORIZATION\n";
echo "========================================================\n";

$policy = new OrderPolicy;
$firstNikitaOrder = $nikitaOrders->first();
$firstDeryOrder = $deryOrders->first();

echo 'Nikita akses order Nikita? '.($policy->view($nikita, $firstNikitaOrder) ? '✅ DIIZINKAN' : '❌ DITOLAK')."\n";
echo 'Nikita akses order Dery?   '.($policy->view($nikita, $firstDeryOrder) ? '❌ DIIZINKAN (BOCOR)' : '✅ DITOLAK (ISOLASI BERHASIL)')."\n";
echo 'Admin akses order Nikita?  '.($policy->view($admin, $firstNikitaOrder) ? '✅ DIIZINKAN' : '❌ DITOLAK')."\n";
echo 'Admin akses order Dery?    '.($policy->view($admin, $firstDeryOrder) ? '✅ DIIZINKAN' : '❌ DITOLAK')."\n";
echo 'Nikita bisa create order?  '.($policy->create($nikita) ? '✅ DIIZINKAN (Memiliki Shift Open)' : '❌ DITOLAK')."\n";
echo 'Robin bisa create order?   '.($policy->create($robin) ? '❌ DIIZINKAN' : '✅ DITOLAK (Shift Sudah Closed)')."\n";
