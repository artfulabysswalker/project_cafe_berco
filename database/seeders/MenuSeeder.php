<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'nama_menu' => 'Espresso',
                'harga' => 15000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Cappuccino',
                'harga' => 20000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Latte',
                'harga' => 22000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Americano',
                'harga' => 18000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Mocha',
                'harga' => 25000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Croissant',
                'harga' => 12000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Sandwich',
                'harga' => 18000,
                'status_tersedia' => true,
            ],
            [
                'nama_menu' => 'Cake Coklat',
                'harga' => 15000,
                'status_tersedia' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}