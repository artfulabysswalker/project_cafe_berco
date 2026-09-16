<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

echo "====================================================\n";
echo "TESTING WEB SHIFTS PAGE RENDERING\n";
echo "====================================================\n";

$admin = User::where('username', 'admin')->first();
$nikita = User::where('username', 'nikita')->first();

echo 'Admin User: '.($admin ? $admin->name : 'Not Found')."\n";
echo 'Nikita User: '.($nikita ? $nikita->name : 'Not Found')."\n";

// Test rendering view directly via view() helper
auth()->login($admin);
$renderedAdmin = view('admin.shifts', [
    'shifts' => CashierShift::with('user')->paginate(10),
    'employees' => User::all(),
    'leaderboard' => collect(),
    'activeShifts' => CashierShift::where('status', 'open')->with('user')->get(),
    'shift1Stats' => ['count' => 1, 'total_omzet' => 74000, 'total_cash' => 52000, 'total_qris' => 22000],
    'shift2Stats' => ['count' => 2, 'active_count' => 2, 'total_omzet' => 134000, 'total_cash' => 82000, 'total_qris' => 52000],
    'myActiveShift' => null,
])->render();

echo 'Admin View Rendered: '.strlen($renderedAdmin)." bytes ✅\n";
if (strpos($renderedAdmin, 'Monitoring Shift Kasir') !== false) {
    echo " -> Header 'Monitoring Shift Kasir' verified! ✅\n";
}
if (strpos($renderedAdmin, 'Station Kasir Aktif Real-Time') !== false) {
    echo " -> 'Station Kasir Aktif Real-Time' card verified! ✅\n";
}

// Test rendering for Kasir Nikita
auth()->login($nikita);
$renderedNikita = view('admin.shifts', [
    'shifts' => CashierShift::where('user_id', $nikita->id_user)->with('user')->paginate(10),
    'employees' => User::all(),
    'leaderboard' => collect(),
    'activeShifts' => CashierShift::where('status', 'open')->with('user')->get(),
    'shift1Stats' => ['count' => 1, 'total_omzet' => 74000, 'total_cash' => 52000, 'total_qris' => 22000],
    'shift2Stats' => ['count' => 2, 'active_count' => 2, 'total_omzet' => 134000, 'total_cash' => 82000, 'total_qris' => 52000],
    'myActiveShift' => $nikita->activeShift,
])->render();

echo 'Nikita Kasir View Rendered: '.strlen($renderedNikita)." bytes ✅\n";
if (strpos($renderedNikita, 'Sesi Kasir: Nikita') !== false) {
    echo " -> Kasir Private Drawer Header 'Sesi Kasir: Nikita' verified! ✅\n";
}
