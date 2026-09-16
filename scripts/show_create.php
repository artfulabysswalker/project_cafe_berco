<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$tables = ['cart_items', 'orders', 'order_items', 'products'];
foreach ($tables as $t) {
    echo "=== $t ===\n";
    $row = DB::select("SHOW CREATE TABLE {$t}");
    print_r($row[0]);
    echo "\n\n";
}
