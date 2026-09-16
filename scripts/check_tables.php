<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

$tables = ['users', 'roles', 'menus', 'cart_items', 'orders', 'order_items', 'vouchers', 'user_vouchers', 'playlists', 'playlist_votes', 'rewards', 'redemptions', 'reviews', 'referrals'];
foreach ($tables as $t) {
    echo $t.': '.(Schema::hasTable($t) ? 'yes' : 'no').PHP_EOL;
}
