<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffShiftSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['role_name' => 'Admin']);
        $staffRole = Role::firstOrCreate(['role_name' => 'Staff']);

        // 1. Ensure Admin Account
        $admin = User::firstOrCreate(['username' => 'admin1'], [
            'name' => 'Main Admin',
            'email' => 'admin@bercocafe.com',
            'id_role' => $adminRole->id_role,
            'password' => Hash::make('password123'),
        ]);

        // 2. Ensure Cashier Staff Account
        $cashier = User::firstOrCreate(['username' => 'staff1'], [
            'name' => 'Cashier Staff',
            'email' => 'staff1@bercocafe.com',
            'id_role' => $staffRole->id_role,
            'password' => Hash::make('password123'),
        ]);

        // 3. Ensure Robin (Kasir Shift 1 - Pagi)
        $robin = User::firstOrCreate(['username' => 'robin'], [
            'name' => 'Robin',
            'email' => 'robin@bercocafe.com',
            'id_role' => $staffRole->id_role,
            'password' => Hash::make('password123'),
        ]);

        // 4. Ensure Dery (Kasir Shift 2 - Sore/Malam)
        $dery = User::firstOrCreate(['username' => 'dery'], [
            'name' => 'Dery',
            'email' => 'dery@bercocafe.com',
            'id_role' => $staffRole->id_role,
            'password' => Hash::make('password123'),
        ]);

        // Assign some orders to Robin (Shift 1 Pagi: 07:00 - 15:00) and Dery (Shift 2 Sore: 15:00 - 23:00)
        $menus = Menu::all();
        if ($menus->isNotEmpty()) {
            // Seed Robin's shift transactions (Pagi)
            $robinOrders = [
                ['nama' => 'din', 'time' => '09:30', 'method' => 'cash', 'items' => [0, 1]],
                ['nama' => 'Pelanggan Umum', 'time' => '10:15', 'method' => 'cash', 'items' => [2]],
                ['nama' => 'Meja 2', 'time' => '11:45', 'method' => 'qris', 'items' => [0, 3]],
                ['nama' => 'budak kelik', 'time' => '13:10', 'method' => 'cash', 'items' => [1]],
            ];

            foreach ($robinOrders as $data) {
                $date = Carbon::today()->setTimeFromTimeString($data['time']);
                $order = Order::create([
                    'tanggal' => $date,
                    'nama_pelanggan' => $data['nama'],
                    'status_pembayaran' => 'paid',
                    'status_order' => 'completed',
                    'payment_method' => $data['method'],
                    'service_type' => 'dine_in',
                    'id_user' => $robin->id_user,
                    'total_harga' => 0,
                ]);

                $total = 0;
                foreach ($data['items'] as $idx) {
                    $menu = $menus[$idx % $menus->count()];
                    $sub = $menu->harga * 1;
                    $total += $sub;
                    OrderItem::create([
                        'id_order' => $order->id_order,
                        'id_menu' => $menu->id_menu,
                        'quantity' => 1,
                        'subtotal' => $sub,
                    ]);
                }
                $order->update(['total_harga' => $total, 'subtotal' => $total]);
            }

            // Seed Dery's shift transactions (Sore / Malam)
            $deryOrders = [
                ['nama' => 'Meja 5', 'time' => '16:20', 'method' => 'qris', 'items' => [0, 2]],
                ['nama' => 'Pelanggan Umum', 'time' => '17:45', 'method' => 'cash', 'items' => [3, 4]],
                ['nama' => 'Kak Sarah', 'time' => '19:10', 'method' => 'cash', 'items' => [1, 2]],
                ['nama' => 'Meja 1', 'time' => '20:35', 'method' => 'qris', 'items' => [0]],
            ];

            foreach ($deryOrders as $data) {
                $date = Carbon::today()->setTimeFromTimeString($data['time']);
                $order = Order::create([
                    'tanggal' => $date,
                    'nama_pelanggan' => $data['nama'],
                    'status_pembayaran' => 'paid',
                    'status_order' => 'completed',
                    'payment_method' => $data['method'],
                    'service_type' => 'dine_in',
                    'id_user' => $dery->id_user,
                    'total_harga' => 0,
                ]);

                $total = 0;
                foreach ($data['items'] as $idx) {
                    $menu = $menus[$idx % $menus->count()];
                    $sub = $menu->harga * 1;
                    $total += $sub;
                    OrderItem::create([
                        'id_order' => $order->id_order,
                        'id_menu' => $menu->id_menu,
                        'quantity' => 1,
                        'subtotal' => $sub,
                    ]);
                }
                $order->update(['total_harga' => $total, 'subtotal' => $total]);
            }

            // Seed Shift Expenses for Robin and Dery
            Expense::create([
                'tanggal' => Carbon::today()->setTime(10, 00),
                'deskripsi' => 'Beli Es Batu Kristal 1 Karung (Shift 1)',
                'kategori' => 'Bahan Baku',
                'nominal' => 10000,
                'operator' => 'Robin (Kasir Shift 1)',
                'id_user' => $robin->id_user,
            ]);

            Expense::create([
                'tanggal' => Carbon::today()->setTime(18, 30),
                'deskripsi' => 'Beli Cup & Plastik Takeaway (Shift 2)',
                'kategori' => 'Perlengkapan',
                'nominal' => 15000,
                'operator' => 'Dery (Kasir Shift 2)',
                'id_user' => $dery->id_user,
            ]);
        }
    }
}
