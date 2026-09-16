<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Biji Kopi Arabika Ijen Honey',
                'unit' => 'gr',
                'purchase_price' => 180.00, // Rp 180.000 / kg -> Rp 180 / gr
                'stock_quantity' => 15000,
                'notes' => 'Pack 1kg roast bean',
            ],
            [
                'name' => 'Biji Kopi Espresso House Blend',
                'unit' => 'gr',
                'purchase_price' => 140.00, // Rp 140.000 / kg -> Rp 140 / gr
                'stock_quantity' => 25000,
                'notes' => 'Blend 60 Arabica / 40 Robusta',
            ],
            [
                'name' => 'Susu Fresh Milk (Greenfields/Diamond)',
                'unit' => 'ml',
                'purchase_price' => 22.00, // Rp 22.000 / Liter -> Rp 22 / ml
                'stock_quantity' => 40000,
                'notes' => 'Tetra pak 1 Liter',
            ],
            [
                'name' => 'Oat Milk (Oatside Barista Blend)',
                'unit' => 'ml',
                'purchase_price' => 42.00, // Rp 42.000 / Liter -> Rp 42 / ml
                'stock_quantity' => 12000,
                'notes' => 'Plant-based milk 1L',
            ],
            [
                'name' => 'Gula Aren Cair Organik',
                'unit' => 'ml',
                'purchase_price' => 35.00, // Rp 35.000 / Liter -> Rp 35 / ml
                'stock_quantity' => 8000,
                'notes' => 'Nira aren murni Banyuwangi',
            ],
            [
                'name' => 'Sirup Karamel Gold (Monin)',
                'unit' => 'ml',
                'purchase_price' => 185.00, // Rp 130.000 / 700ml -> Rp 185 / ml
                'stock_quantity' => 2800,
                'notes' => 'Botol kaca 700ml',
            ],
            [
                'name' => 'Matcha Powder Ceremonial Uji',
                'unit' => 'gr',
                'purchase_price' => 650.00, // Rp 650 / gr
                'stock_quantity' => 1500,
                'notes' => 'Tins 500gr',
            ],
            [
                'name' => 'Cup + Lid + Straw Biodegradable (14oz)',
                'unit' => 'pcs',
                'purchase_price' => 1250.00, // Rp 1.250 / set
                'stock_quantity' => 1200,
                'notes' => 'Cold beverage packaging',
            ],
            [
                'name' => 'Frozen Dough Croissant Isigny',
                'unit' => 'pcs',
                'purchase_price' => 8500.00,
                'stock_quantity' => 150,
                'notes' => 'Ready to bake French pastry',
            ],
        ];

        foreach ($materials as $data) {
            RawMaterial::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // Link sample recipes to existing menus
        $kopiSusu = Menu::where('nama_menu', 'like', '%Gula Aren%')->orWhere('nama_menu', 'like', '%Kopi Susu%')->first();
        if ($kopiSusu) {
            $bean = RawMaterial::where('name', 'like', '%House Blend%')->first();
            $milk = RawMaterial::where('name', 'like', '%Fresh Milk%')->first();
            $aren = RawMaterial::where('name', 'like', '%Gula Aren%')->first();
            $cup = RawMaterial::where('name', 'like', '%Cup%')->first();

            $kopiSusu->recipes()->delete();
            if ($bean) {
                ProductRecipe::create(['menu_id' => $kopiSusu->id_menu, 'raw_material_id' => $bean->id, 'quantity_used' => 18]);
            } // 18gr coffee
            if ($milk) {
                ProductRecipe::create(['menu_id' => $kopiSusu->id_menu, 'raw_material_id' => $milk->id, 'quantity_used' => 120]);
            } // 120ml milk
            if ($aren) {
                ProductRecipe::create(['menu_id' => $kopiSusu->id_menu, 'raw_material_id' => $aren->id, 'quantity_used' => 25]);
            } // 25ml aren
            if ($cup) {
                ProductRecipe::create(['menu_id' => $kopiSusu->id_menu, 'raw_material_id' => $cup->id, 'quantity_used' => 1]);
            } // 1 cup

            $kopiSusu->updateHppFromRecipe();
        }
    }
}
