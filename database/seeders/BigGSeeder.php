<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BigGSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $pick = fn($arr) => $arr[array_rand($arr)];

        /*
        | ROLES
        */
        DB::table('roles')->insert([
            ['role_name' => 'admin'],
            ['role_name' => 'customer'],
            ['role_name' => 'staff'],
        ]);

        $roles = DB::table('roles')->get();

        /*
        | USERS
        */
        $users = [];

        for ($i = 1; $i <= 20; $i++) {
            $users[] = [
                'name' => "User $i",
                'username' => "user$i",
                'email' => "user$i@mail.com",
                'password' => bcrypt('password'),
                'id_role' => $roles->random()->id_role,
                'exp' => rand(0, 5000),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);
        $users = DB::table('users')->get();

        /*
        | MENUS
        */
        $menuNames = ['Cappuccino', 'Latte', 'Espresso', 'Mocha', 'Americano'];

        foreach ($menuNames as $name) {
            DB::table('menus')->insert([
                'nama_menu' => $name,
                'harga' => rand(20000, 50000),
                'status_tersedia' => 1,
                'rating' => rand(35, 50) / 10,
                'deskripsi' => "$name special coffee",
                'foto' => strtolower($name) . '.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $menus = DB::table('menus')->get();

        /*
        | ✅ FIXED ENUMS (IMPORTANT PART)
        */
        $orderStatuses = ['pending', 'completed'];

        // 🔥 FIX: must match DB column definition
        // If your DB is ENUM('pending','paid') this fixes your error
        $paymentStatuses = ['pending', 'paid'];

        /*
        | ORDERS
        */
        DB::table('orders')->insert(
            collect(range(1, 30))->map(function () use ($users, $orderStatuses, $paymentStatuses) {

                $user = $users->random();

                return [
                    'tanggal' => now()->subDays(rand(0, 30)),
                    'nama_pelanggan' => $user->name,
                    'total_harga' => 0,
                    'status_pembayaran' => $paymentStatuses[array_rand($paymentStatuses)],
                    'status_order' => $orderStatuses[array_rand($orderStatuses)],
                    'id_user' => $user->id_user,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );

        $orders = DB::table('orders')->get();

        /*
        | ORDER ITEMS
        */
        foreach ($orders as $order) {

            $itemCount = rand(1, 3);
            $total = 0;

            for ($i = 0; $i < $itemCount; $i++) {

                $menu = $menus->random();
                $qty = rand(1, 3);
                $subtotal = $menu->harga * $qty;

                DB::table('order_items')->insert([
                    'id_order' => $order->id_order,
                    'id_menu' => $menu->id_menu,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $total += $subtotal;
            }

            DB::table('orders')
                ->where('id_order', $order->id_order)
                ->update(['total_harga' => $total]);
        }

        /*
        | PAYMENTS
        */
        foreach ($orders as $order) {

            DB::table('payments')->insert([
                'id_order' => $order->id_order,
                'metode_pembayaran' => $pick(['cash', 'qris', 'card']),
                'jumlah_bayar' => $order->total_harga ?? 0,
                'tanggal_bayar' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        | CART
        */
        foreach ($users as $user) {
            DB::table('cart_items')->insert([
                'user_id' => $user->id_user,
                'menu_id' => $menus->random()->id_menu,
                'quantity' => rand(1, 3),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        | REVIEWS
        */
        foreach (range(1, 40) as $i) {
            DB::table('reviews')->insert([
                'user_id' => $users->random()->id_user,
                'menu_id' => $menus->random()->id_menu,
                'rating' => rand(3, 5),
                'comment' => 'Nice product!',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        | REWARDS
        */
        DB::table('rewards')->insert([
            [
                'name' => 'Bronze',
                'description' => 'Starter reward',
                'exp_cost' => 100,
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silver',
                'description' => 'Mid reward',
                'exp_cost' => 500,
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        | VOUCHERS
        */
        DB::table('vouchers')->insert([
            [
                'code' => strtoupper(Str::random(8)),
                'name' => 'WELCOME10',
                'description' => '10% discount',
                'discount_percentage' => 10,
                'is_active' => 1,
                'valid_from' => now(),
                'valid_until' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        | REFERRALS
        */
        foreach (range(1, 10) as $i) {
            DB::table('referrals')->insert([
                'referral_code' => strtoupper(Str::random(10)),
                'referrer_id' => $users->random()->id_user,
                'referee_id' => $users->random()->id_user,
                'status' => 'active',
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}