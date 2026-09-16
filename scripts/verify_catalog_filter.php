<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$testUrls = [
    '/menu' => 'SEMUA',
    '/menu?category=classic-coffee' => 'CLASSIC COFFEE',
    '/menu?category=non-coffee' => 'NON COFFEE',
    '/menu?category=food' => 'FOOD',
    '/menu?category=snack' => 'SNACK',
    '/menu?category=dessert' => 'DESSERT',
    '/menu?search=croissant' => 'SEARCH: CROISSANT',
    '/menu?search=kopi' => 'SEARCH: KOPI',
];

echo "=== TEST QUERY FILTERING & SECTION GENERATION ===\n";

foreach ($testUrls as $url => $label) {
    $req = Request::create($url, 'GET');
    $req->setLaravelSession(app('session.store'));
    $resp = $app->handle($req);
    $viewData = $resp->original->getData();

    $cats = $viewData['categories'];
    $totalProds = $viewData['totalProductsCount'];

    echo "URL: {$url} ({$label})\n";
    echo " -> Total Produk Tampil: {$totalProds}\n";
    foreach ($cats as $c) {
        if ($c->products->isNotEmpty()) {
            echo "    * Section [{$c->nama_kategori}]: ".$c->products->pluck('nama_menu')->implode(', ')."\n";
        }
    }
    echo "----------------------------------------------------------------------\n";
}
