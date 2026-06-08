<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxConfiguration;
use App\Models\DiscountScheme;
use App\Models\User;
use Carbon\Carbon;

class TaxDiscountSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('id_role', 1)->first() ?? User::first();

        // Upsert tax configurations
        TaxConfiguration::updateOrInsert(
            ['name' => 'PB1 - Pajak Pertambahan Nilai (10%)'],
            [
                'tax_percentage' => 10,
                'description' => 'Pajak standar untuk semua transaksi penjualan di cafe',
                'is_active' => true,
                'effective_from' => now(),
                'id_user' => $admin->id_user,
            ]
        );

        TaxConfiguration::updateOrInsert(
            ['name' => 'PB1 - Pajak Pertambahan Nilai (5%)'],
            [
                'tax_percentage' => 5,
                'description' => 'Pajak khusus untuk promosi musiman',
                'is_active' => false,
                'effective_from' => now(),
                'id_user' => $admin->id_user,
            ]
        );

        // Upsert discount schemes
        DiscountScheme::updateOrInsert(
            ['code' => 'PAGI_SPESIAL'],
            [
                'name' => 'Promo Pagi Spesial',
                'description' => 'Diskon 15% untuk pembelian antara jam 06:00 - 10:00',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_purchase' => 50000,
                'max_discount' => 30000,
                'max_uses' => 100,
                'is_active' => true,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(1),
                'id_user' => $admin->id_user,
            ]
        );

        DiscountScheme::updateOrInsert(
            ['code' => 'SIANG_HEMAT'],
            [
                'name' => 'Hemat Siang Hari',
                'description' => 'Diskon Rp 20.000 untuk pembelian minimal Rp 100.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'min_purchase' => 100000,
                'max_uses' => 50,
                'is_active' => true,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(2),
                'id_user' => $admin->id_user,
            ]
        );

        DiscountScheme::updateOrInsert(
            ['code' => 'WEEKEND_MERIAH'],
            [
                'name' => 'Weekend Meriah',
                'description' => 'Diskon 20% untuk pembelian akhir pekan (Sabtu-Minggu)',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_purchase' => 75000,
                'max_discount' => 50000,
                'max_uses' => 200,
                'is_active' => true,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'id_user' => $admin->id_user,
            ]
        );

        DiscountScheme::updateOrInsert(
            ['code' => 'LOYALITAS10'],
            [
                'name' => 'Member Setia',
                'description' => 'Diskon 10% untuk member setia kami',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_purchase' => 0,
                'max_discount' => 100000,
                'is_active' => true,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
                'id_user' => $admin->id_user,
            ]
        );

        $this->command->info('Tax dan Discount seeders berhasil dibuat!');
    }
}
