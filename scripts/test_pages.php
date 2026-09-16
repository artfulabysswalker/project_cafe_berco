<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo '--- 1. Testing as Guest / Public Visitor ---'.PHP_EOL;
Auth::logout();
session()->flush();

$publicRoutes = [
    '/' => 'Landing Page / Beranda',
    '/login' => 'Customer / Staff Login Page',
    '/register' => 'Customer Registration Page',
    '/staff-login' => 'Staff Login Page',
    '/menu' => 'Katalog Menu Customer (Semua)',
    '/menu?category=classic-coffee' => 'Katalog Menu (Filter: Classic Coffee)',
    '/cart' => 'Keranjang Belanja Customer (Guest Mode)',
];

foreach ($publicRoutes as $url => $label) {
    Auth::logout();
    $req = Request::create($url, 'GET');
    $req->setLaravelSession(app('session.store'));
    $resp = $app->handle($req);
    $status = $resp->getStatusCode();
    $loc = $resp->headers->get('Location');
    echo ($status === 200 ? '✅ [200 OK]' : "❌ [{$status} -> {$loc}]")." {$label} ({$url})".PHP_EOL;
}

echo PHP_EOL.'--- 2. Testing as Admin ---'.PHP_EOL;
$admin = User::where('username', 'admin')->first();
Auth::login($admin);

$adminRoutes = [
    '/admin/dashboard' => 'Admin Dashboard',
    '/admin/menu' => 'Daftar Menu & Modal Tambah',
    '/admin/hpp' => 'Kalkulasi HPP & Manajemen Resep',
    '/admin/stats' => 'Statistik & Analitik Laba',
    '/admin/staff' => 'Manajemen Staff',
];

foreach ($adminRoutes as $url => $label) {
    $req = Request::create($url, 'GET');
    $req->setLaravelSession(app('session.store'));
    $resp = $app->handle($req);
    $status = $resp->getStatusCode();
    echo ($status === 200 ? '✅ [200 OK]' : "❌ [{$status}]")." {$label} ({$url})".PHP_EOL;
}
