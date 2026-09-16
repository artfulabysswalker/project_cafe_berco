<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

$staffList = User::whereHas('role', function ($q) {
    $q->whereIn('role_name', ['Admin', 'Staff', 'Cashier', 'kasir', 'pegawai']);
})->orWhereIn('username', ['admin', 'robin', 'nikita', 'dery'])
    ->with('role')
    ->get()
    ->unique('name');

echo "===================================================\n";
echo 'STAFF LIST FOR /admin/stats DROPDOWN ('.$staffList->count()." Staf):\n";
echo "===================================================\n";
foreach ($staffList as $st) {
    echo sprintf("ID: %-3d | Username: %-8s | Name: %-20s | Role: %s\n",
        $st->id_user, $st->username, $st->name, $st->role ? $st->role->role_name : 'No Role'
    );
}
