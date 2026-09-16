<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

$admin = User::where('email', 'admin@bercocafe.com')->orWhere('username', 'admin')->first();
echo 'Admin found: '.($admin ? $admin->email : 'NONE').PHP_EOL;

if ($admin) {
    echo 'Current stored hash: '.$admin->password.PHP_EOL;
    echo "Hash::check('adminberco123'): ".(Hash::check('adminberco123', $admin->password) ? 'TRUE' : 'FALSE').PHP_EOL;

    // Test Auth::attempt with email
    $attemptEmail = Auth::attempt(['email' => 'admin@bercocafe.com', 'password' => 'adminberco123']);
    echo 'Auth::attempt by email: '.($attemptEmail ? 'SUCCESS' : 'FAILED').PHP_EOL;

    // Test Auth::attempt with username
    $attemptUser = Auth::attempt(['username' => 'admin', 'password' => 'adminberco123']);
    echo 'Auth::attempt by username: '.($attemptUser ? 'SUCCESS' : 'FAILED').PHP_EOL;
}
