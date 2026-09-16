<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

echo '=== TEST LOGIN ==='.PHP_EOL;

$cases = [
    ['input' => 'admin@bercocafe.com', 'password' => 'adminberco123', 'desc' => 'Admin via Email'],
    ['input' => 'admin', 'password' => 'adminberco123', 'desc' => 'Admin via Username'],
    ['input' => 'dery@bercocafe.com', 'password' => '123456', 'desc' => 'Cashier Dery via Email'],
    ['input' => 'dery', 'password' => '123456', 'desc' => 'Cashier Dery via Username'],
    ['input' => 'robin', 'password' => '123456', 'desc' => 'Cashier Robin via Username'],
    ['input' => 'nikita', 'password' => '123456', 'desc' => 'Cashier Nikita via Username'],
];

foreach ($cases as $c) {
    $input = $c['input'];
    $pwd = $c['password'];
    $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
    $field = $isEmail ? 'email' : 'username';
    $fallback = $isEmail ? 'username' : 'email';

    $ok = Auth::attempt([$field => $input, 'password' => $pwd]) || Auth::attempt([$fallback => $input, 'password' => $pwd]);
    $user = Auth::user();
    $role = $user?->role?->role_name ?? '-';

    echo ($ok ? '✅ [BERHASIL]' : '❌ [GAGAL]')." {$c['desc']} -> Logged in as: ".($user ? "{$user->name} ({$role})" : 'None').PHP_EOL;
    Auth::logout();
}
