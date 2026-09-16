<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportDemoSeeder extends Seeder
{
    public function run(): void
    {
        $menus = Menu::all();
        if ($menus->isEmpty()) {
            return;
        }

        $cashier = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Staff');
        })->first();

        $cashierId = $cashier ? $cashier->id_user : null;
        $cashierName = 'Gina (Kasir)';

        // Sample Cash Orders
        $cashData = [
            ['nama' => 'din', 'time' => '12:34', 'items' => [0, 1]],
            ['nama' => 'budak kelik', 'time' => '12:25', 'items' => [2]],
            ['nama' => 'Pelanggan Umum', 'time' => '10:59', 'items' => [0, 3]],
            ['nama' => 'Meja 1', 'time' => '10:51', 'items' => [1]],
            ['nama' => 'Pelanggan Umum', 'time' => '10:13', 'items' => [4]],
            ['nama' => 'Meja 1', 'time' => '10:02', 'items' => [0]],
            ['nama' => 'Pelanggan Umum', 'time' => '08:51', 'items' => [1]],
        ];

        foreach ($cashData as $data) {
            $date = Carbon::today()->setTimeFromTimeString($data['time']);
            $order = Order::create([
                'tanggal' => $date,
                'nama_pelanggan' => $data['nama'],
                'status_pembayaran' => 'paid',
                'status_order' => 'completed',
                'payment_method' => 'cash',
                'service_type' => 'dine_in',
                'id_user' => $cashierId,
                'total_harga' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $menuIndex) {
                $menu = $menus[$menuIndex % $menus->count()];
                $qty = 1;
                $subtotal = $menu->harga * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_menu' => $menu->id_menu,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_harga' => $total, 'subtotal' => $total]);
        }

        // Sample QRIS Orders
        $qrisData = [
            ['nama' => 'Meja 3', 'time' => '15:09', 'items' => [0, 2]],
            ['nama' => 'din', 'time' => '13:21', 'items' => [1]],
            ['nama' => 'Pelanggan Umum', 'time' => '10:26', 'items' => [3, 4]],
        ];

        foreach ($qrisData as $data) {
            $date = Carbon::today()->setTimeFromTimeString($data['time']);
            $order = Order::create([
                'tanggal' => $date,
                'nama_pelanggan' => $data['nama'],
                'status_pembayaran' => 'paid',
                'status_order' => 'completed',
                'payment_method' => 'qris',
                'service_type' => 'take_away',
                'id_user' => $cashierId,
                'total_harga' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $menuIndex) {
                $menu = $menus[$menuIndex % $menus->count()];
                $qty = 1;
                $subtotal = $menu->harga * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_menu' => $menu->id_menu,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_harga' => $total, 'subtotal' => $total]);
        }

        // Sample Expenses
        if (Expense::count() === 0) {
            Expense::create([
                'tanggal' => Carbon::today()->setTime(8, 30),
                'deskripsi' => 'Beli Es Batu Kristal 2 Karung',
                'kategori' => 'Bahan Baku',
                'nominal' => 20000,
                'operator' => 'Gina (Kasir Shift 1)',
                'id_user' => $cashierId,
            ]);

            Expense::create([
                'tanggal' => Carbon::today()->setTime(11, 15),
                'deskripsi' => 'Isi Ulang Gas LPG 3kg',
                'kategori' => 'Operasional',
                'nominal' => 22000,
                'operator' => 'Gina (Kasir Shift 1)',
                'id_user' => $cashierId,
            ]);

            Expense::create([
                'tanggal' => Carbon::today()->setTime(14, 00),
                'deskripsi' => 'Beli Cup Plastik & Sedotan Kopi',
                'kategori' => 'Perlengkapan',
                'nominal' => 35000,
                'operator' => 'Gina (Kasir Shift 1)',
                'id_user' => $cashierId,
            ]);
        }
    }
}
