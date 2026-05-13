<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Berco',
            'email' => 'admin@berco.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'exp' => 1000,
        ]);
    }
}