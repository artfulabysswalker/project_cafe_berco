<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        DB::table('menus')->insert([

            [
                'nama_menu' => 'Espresso',
                'harga' => 15000,
                'status_tersedia' => 1,
                'foto' => null,
                'rating' => 0,
                'deskripsi' => 'Strong black coffee',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'nama_menu' => 'Cappuccino',
                'harga' => 22000,
                'status_tersedia' => 1,
                'foto' => null,
                'rating' => 0,
                'deskripsi' => 'Coffee with milk foam',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'nama_menu' => 'Burger',
                'harga' => 30000,
                'status_tersedia' => 1,
                'foto' => null,
                'rating' => 0,
                'deskripsi' => 'Beef burger',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'nama_menu' => 'French Fries',
                'harga' => 18000,
                'status_tersedia' => 1,
                'foto' => null,
                'rating' => 0,
                'deskripsi' => 'Crispy fries',
                'created_at' => now(),
                'updated_at' => now()
            ]

        ]);
    }
}