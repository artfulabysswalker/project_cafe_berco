<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([

            [
                'tanggal' => now(),
                'nama_pelanggan' => 'Matt',
                'total_harga' => 25000,
                'status_pembayaran' => 'Sudah',
                'status_order' => 'pending',
                'id_user' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'tanggal' => now(),
                'nama_pelanggan' => 'Rina',
                'total_harga' => 40000,
                'status_pembayaran' => 'Sudah',
                'status_order' => 'pending',
                'id_user' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'tanggal' => now(),
                'nama_pelanggan' => 'Doni',
                'total_harga' => 30000,
                'status_pembayaran' => 'Sudah',
                'status_order' => 'completed',
                'id_user' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}