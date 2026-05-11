<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Comeback voucher untuk pelanggan tidak aktif
        Voucher::create([
            'code' => 'COMEBACK30',
            'name' => 'Promo Comeback - Diskon 20%',
            'description' => 'Dapatkan diskon 20% untuk pelanggan setia yang tidak aktif selama 30 hari',
            'discount_percentage' => 20,
            'max_discount' => 100000,
            'min_purchase' => 50000,
            'max_uses' => null,
            'used_count' => 0,
            'type' => 'comeback',
            'valid_from' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(90)->format('Y-m-d'),
            'is_active' => true,
        ]);

        // Welcome voucher untuk customer baru
        Voucher::create([
            'code' => 'WELCOME15',
            'name' => 'Promo Selamat Datang',
            'description' => 'Dapatkan diskon 15% untuk pembelian pertama Anda',
            'discount_percentage' => 15,
            'max_discount' => 75000,
            'min_purchase' => 50000,
            'max_uses' => null,
            'used_count' => 0,
            'type' => 'welcome',
            'valid_from' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(180)->format('Y-m-d'),
            'is_active' => true,
        ]);

        // Referral voucher
        Voucher::create([
            'code' => 'REFERRAL25',
            'name' => 'Bonus Referral - Diskon 25%',
            'description' => 'Dapatkan diskon 25% ketika Anda mereferensikan teman',
            'discount_percentage' => 25,
            'max_discount' => 150000,
            'min_purchase' => 100000,
            'max_uses' => null,
            'used_count' => 0,
            'type' => 'referral',
            'valid_from' => now()->format('Y-m-d'),
            'valid_until' => now()->addYear()->format('Y-m-d'),
            'is_active' => true,
        ]);

        // Flash sale voucher
        Voucher::create([
            'code' => 'FLASHSALE50',
            'name' => 'Flash Sale - Diskon Rp 50.000',
            'description' => 'Penawaran terbatas! Diskon Rp 50.000 untuk pembelian hari ini',
            'discount_amount' => 50000,
            'min_purchase' => 100000,
            'max_uses' => 100,
            'used_count' => 0,
            'type' => 'promotion',
            'valid_from' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(7)->format('Y-m-d'),
            'is_active' => true,
        ]);
    }
}
