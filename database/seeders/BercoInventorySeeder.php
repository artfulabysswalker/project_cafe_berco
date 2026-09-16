<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BercoInventorySeeder extends Seeder
{
    /**
     * Run the database seeds for Cafe Berco Inventory.
     */
    public function run(): void
    {
        $this->call(InventorySeeder::class);
    }
}
