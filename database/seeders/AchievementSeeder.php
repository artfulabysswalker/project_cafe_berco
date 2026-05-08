<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Achievement::create([
            'name' => 'Pemula',
            'slug' => 'pemula',
            'description' => 'Lakukan pembelian pertama Anda',
            'icon' => '🎯',
            'threshold' => 1,
            'reward_amount' => 5000,
            'type' => 'orders_count',
            'is_active' => true,
        ]);

        Achievement::create([
            'name' => 'Pelanggan Setia',
            'slug' => 'pelanggan-setia',
            'description' => 'Lakukan 10 pembelian',
            'icon' => '⭐',
            'threshold' => 10,
            'reward_amount' => 25000,
            'type' => 'orders_count',
            'is_active' => true,
        ]);

        Achievement::create([
            'name' => 'Penggemar Kopi',
            'slug' => 'penggemar-kopi',
            'description' => 'Total pembelian mencapai 100 ribu',
            'icon' => '☕',
            'threshold' => 100000,
            'reward_amount' => 50000,
            'type' => 'total_spent',
            'is_active' => true,
        ]);

        Achievement::create([
            'name' => 'Duta Kafe',
            'slug' => 'duta-kafe',
            'description' => 'Ajak 5 teman untuk berbelanja',
            'icon' => '👥',
            'threshold' => 5,
            'reward_amount' => 100000,
            'type' => 'referrals_count',
            'is_active' => true,
        ]);

        Achievement::create([
            'name' => 'Atlet Espresso',
            'slug' => 'atlet-espresso',
            'description' => 'Lakukan 50 pembelian',
            'icon' => '🏆',
            'threshold' => 50,
            'reward_amount' => 150000,
            'type' => 'orders_count',
            'is_active' => true,
        ]);
    }
}
