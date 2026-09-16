<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Validator;

echo '=== 1. CEK USER SEEDER RESULT ==='.PHP_EOL;
$users = User::with('role')->get();
foreach ($users as $u) {
    echo "- ID: {$u->id_user} | Username: {$u->username} | Name: {$u->name} | Email: {$u->email} | Role: ".($u->role->role_name ?? '-').PHP_EOL;
}

echo PHP_EOL.'=== 2. CEK VALIDASI DUPLIKAT EMAIL & USERNAME ==='.PHP_EOL;
$validator = Validator::make([
    'name' => 'Duplicate Dery',
    'username' => 'dery',
    'email' => 'dery@bercocafe.com',
], [
    'username' => 'required|string|max:255|alpha_dash|unique:users,username',
    'email' => 'nullable|email|max:255|unique:users,email',
], [
    'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
    'email.unique' => 'Email ini sudah terdaftar di sistem.',
]);

if ($validator->fails()) {
    echo '✅ Validasi Berhasil Mencegah Duplikasi:'.PHP_EOL;
    foreach ($validator->errors()->all() as $err) {
        echo "   - {$err}".PHP_EOL;
    }
} else {
    echo '❌ Error: Validasi gagal mendeteksi duplikat!'.PHP_EOL;
}
