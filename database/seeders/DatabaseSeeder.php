<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@cafeberco.com',
            'role' => 'admin',
            'notification_enabled' => true,
        ]);

        // Create test customer
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
            'notification_enabled' => true,
        ]);

        $this->call(ProductSeeder::class);
    }
}
