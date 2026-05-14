<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Carbon\Carbon;

class StatsTestSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------
        // 1. Create Menus
        // -------------------------
        $menus = [
            ['id_menu' => 1, 'name' => 'Coffee Latte', 'price' => 20000],
            ['id_menu' => 2, 'name' => 'Cappuccino', 'price' => 18000],
            ['id_menu' => 3, 'name' => 'Croissant', 'price' => 15000],
            ['id_menu' => 4, 'name' => 'Sandwich', 'price' => 25000],
            ['id_menu' => 5, 'name' => 'Iced Tea', 'price' => 10000],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['id_menu' => $menu['id_menu']],
                $menu
            );
        }

        // -------------------------
        // 2. Create Orders + Items
        // -------------------------
        for ($i = 1; $i <= 50; $i++) {

            $date = Carbon::now()
                ->subDays(rand(0, 30))
                ->subHours(rand(0, 23));

            $order = Order::create([
                'id_order' => $i,
                'nama_pelanggan' => 'Customer ' . $i,
                'status_order' => 'completed',
                'total_harga' => 0,
                'tanggal' => $date,
            ]);

            $total = 0;

            // 1–3 items per order
            $itemCount = rand(1, 3);

            for ($j = 0; $j < $itemCount; $j++) {

                $menu = $menus[array_rand($menus)];
                $qty = rand(1, 3);
                $subtotal = $menu['price'] * $qty;

                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_menu' => $menu['id_menu'],
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            // update total
            $order->update([
                'total_harga' => $total
            ]);
        }
    }
}