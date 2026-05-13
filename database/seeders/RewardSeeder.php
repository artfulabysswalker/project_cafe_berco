<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reward;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        Reward::insert([
            [
                'name' => 'Voucher Diskon 10%',
                'description' => 'Dapatkan potongan 10% untuk pembelian menu apa saja.',
                'exp_cost' => 200,
                'discount_percentage' => 10,
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Voucher Diskon 15%',
                'description' => 'Potongan 15% untuk transaksi lebih dari Rp 50.000.',
                'exp_cost' => 350,
                'discount_percentage' => 15,
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Voucher Diskon 20%',
                'description' => 'Potongan 20% untuk menu pilihan tertentu.',
                'exp_cost' => 500,
                'discount_percentage' => 20,
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
