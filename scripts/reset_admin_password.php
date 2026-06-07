<?php

require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use Illuminate's Artisan
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Get or create admin user
$admin = User::where('username', 'admin1')->first();

if (!$admin) {
    // Get roles
    $roles = \DB::table('roles')->get();
    $adminRole = $roles->where('role_name', 'Admin')->first();
    
    // Create admin user
    $admin = User::create([
        'name' => 'Main Admin',
        'username' => 'admin1',
        'email' => 'admin1@email.com',
        'password' => Hash::make('password'),
        'id_role' => $adminRole->id_role ?? 1
    ]);
    echo "Admin user created!\n";
} else {
    // Update password
    $admin->update(['password' => Hash::make('password')]);
    echo "Admin password updated!\n";
}

echo "Username: admin1\n";
echo "Email: admin1@email.com\n";
echo "Password: password\n";
