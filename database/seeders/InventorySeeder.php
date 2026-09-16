<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds for Cafe Berco Inventory (Bahan Baku & Kemasan).
     */
    public function run(): void
    {
        $items = [
            [
                'item_code' => 'ING-001',
                'item_name' => 'Biji Kopi House Blend',
                'category' => 'Bahan Minuman',
                'current_stock' => 4500,
                'min_stock' => 1000,
                'unit' => 'Gram',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-002',
                'item_name' => 'Susu Fresh Milk',
                'category' => 'Bahan Minuman',
                'current_stock' => 12,
                'min_stock' => 5,
                'unit' => 'Liter',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-003',
                'item_name' => 'Sirup Flavour (Caramel/Vanilla/Hazelnut)',
                'category' => 'Sirup & Flavour',
                'current_stock' => 2,
                'min_stock' => 3,
                'unit' => 'Botol',
                'status' => 'menipis',
            ],
            [
                'item_code' => 'ING-004',
                'item_name' => 'Pure Matcha Powder',
                'category' => 'Bahan Minuman',
                'current_stock' => 250,
                'min_stock' => 500,
                'unit' => 'Gram',
                'status' => 'kritis',
            ],
            [
                'item_code' => 'ING-005',
                'item_name' => 'Biang Teh Hitam',
                'category' => 'Bahan Minuman',
                'current_stock' => 3,
                'min_stock' => 2,
                'unit' => 'Liter',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-006',
                'item_name' => 'Sirup Lemon / Konsentrat Buah',
                'category' => 'Sirup & Flavour',
                'current_stock' => 4,
                'min_stock' => 2,
                'unit' => 'Botol',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-007',
                'item_name' => 'Kentang Beku (French Fries)',
                'category' => 'Bahan Makanan',
                'current_stock' => 1500,
                'min_stock' => 2000,
                'unit' => 'Gram',
                'status' => 'menipis',
            ],
            [
                'item_code' => 'ING-008',
                'item_name' => 'Donat Kentang Frozen',
                'category' => 'Bahan Makanan',
                'current_stock' => 24,
                'min_stock' => 10,
                'unit' => 'Pcs',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-009',
                'item_name' => 'Glaze Donat (Choco, Matcha, Tiramisu)',
                'category' => 'Bahan Makanan',
                'current_stock' => 3,
                'min_stock' => 2,
                'unit' => 'Box',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-010',
                'item_name' => 'Daging Ayam Fillet',
                'category' => 'Bahan Makanan',
                'current_stock' => 3000,
                'min_stock' => 1500,
                'unit' => 'Gram',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-011',
                'item_name' => 'Nasi Putih / Beras',
                'category' => 'Bahan Makanan',
                'current_stock' => 10,
                'min_stock' => 5,
                'unit' => 'Kg',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-012',
                'item_name' => 'Mie Telur & Kwetiau',
                'category' => 'Bahan Makanan',
                'current_stock' => 15,
                'min_stock' => 10,
                'unit' => 'Porsi',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-013',
                'item_name' => 'Telur Ayam',
                'category' => 'Bahan Makanan',
                'current_stock' => 30,
                'min_stock' => 15,
                'unit' => 'Butir',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-014',
                'item_name' => 'Risol & Lumpia Frozen',
                'category' => 'Bahan Makanan',
                'current_stock' => 8,
                'min_stock' => 15,
                'unit' => 'Pcs',
                'status' => 'menipis',
            ],
            [
                'item_code' => 'ING-015',
                'item_name' => 'Cireng Salju & Tahu Walik Frozen',
                'category' => 'Bahan Makanan',
                'current_stock' => 20,
                'min_stock' => 10,
                'unit' => 'Porsi',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-016',
                'item_name' => 'Paper Cup Hot 8oz',
                'category' => 'Kemasan',
                'current_stock' => 150,
                'min_stock' => 50,
                'unit' => 'Pcs',
                'status' => 'aman',
            ],
            [
                'item_code' => 'ING-017',
                'item_name' => 'Plastic Cup Cold 16oz',
                'category' => 'Kemasan',
                'current_stock' => 35,
                'min_stock' => 100,
                'unit' => 'Pcs',
                'status' => 'kritis',
            ],
            [
                'item_code' => 'ING-018',
                'item_name' => 'Paper Rice Bowl / Piring Saji',
                'category' => 'Kemasan',
                'current_stock' => 80,
                'min_stock' => 30,
                'unit' => 'Pcs',
                'status' => 'aman',
            ],
        ];

        foreach ($items as $data) {
            Inventory::updateOrCreate(
                ['item_code' => $data['item_code']],
                $data
            );
        }
    }
}
