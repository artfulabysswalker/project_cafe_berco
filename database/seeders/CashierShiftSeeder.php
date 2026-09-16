<?php

namespace Database\Seeders;

use App\Models\CashierShift;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CashierShiftSeeder extends Seeder
{
    public function run(): void
    {
        $cashierRole = Role::firstOrCreate(['role_name' => 'Cashier']);
        $menus = Menu::all();

        // 1. Pastikan Staf Rolling Terdaftar (Robin, Nikita, Dery)
        $staffMembers = [
            'robin' => ['name' => 'Robin', 'email' => 'robin@bercocafe.com'],
            'nikita' => ['name' => 'Nikita', 'email' => 'nikita@bercocafe.com'],
            'dery' => ['name' => 'Dery', 'email' => 'dery@bercocafe.com'],
        ];

        $users = [];
        foreach ($staffMembers as $username => $data) {
            $users[$username] = User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('123456'),
                    'id_role' => $cashierRole->id_role,
                    'status' => 'active',
                ]
            );
        }

        $today = Carbon::today();

        // -------------------------------------------------------------
        // SKENARIO 1: ROBIN (Shift 1: 07:00 - 15:00 - CLOSED)
        // Target: Kas Awal: Rp200.000, Total Penjualan: Rp1.450.000
        // -------------------------------------------------------------
        $robinShift = CashierShift::create([
            'user_id' => $users['robin']->id_user,
            'shift_type' => 'shift_1',
            'starting_cash' => 200000, // Modal awal laci Rp 200.000
            'status' => 'closed',
            'opened_at' => $today->copy()->setTime(7, 0, 0),
            'closed_at' => $today->copy()->setTime(15, 0, 0),
            'notes' => 'Shift 1 pagi selesai. Kas balance sesuai rekonsiliasi.',
        ]);

        $this->seedOrdersForShift($robinShift, $users['robin'], [
            ['customer' => 'Pak Budi (Dine In)', 'time' => '08:15', 'pay_type' => 'cash', 'amount' => 450000],
            ['customer' => 'Meja 03', 'time' => '10:30', 'pay_type' => 'qris', 'amount' => 500000],
            ['customer' => 'Bu Ani (Takeaway)', 'time' => '13:45', 'pay_type' => 'cash', 'amount' => 500000],
        ], $menus);

        // Rekonsiliasi Kas Robin (Tutup Shift)
        $cashSalesRobin = 950000;  // Rp 950.000 tunai
        $nonCashRobin = 500000;    // Rp 500.000 QRIS
        $expectedRobin = $robinShift->starting_cash + $cashSalesRobin; // Rp 1.150.000

        $robinShift->update([
            'cash_sales' => $cashSalesRobin,
            'non_cash_sales' => $nonCashRobin,
            'expected_cash' => $expectedRobin,
            'actual_cash' => $expectedRobin, // Kas sesuai / balance
            'difference' => 0,
        ]);

        // -------------------------------------------------------------
        // SKENARIO 2: NIKITA (Shift 2: 15:00 - 23:00 - OPEN / Kasir A)
        // Target: Kas Awal: Rp200.000, Penjualan Berjalan: Rp820.000
        // -------------------------------------------------------------
        $nikitaShift = CashierShift::create([
            'user_id' => $users['nikita']->id_user,
            'shift_type' => 'shift_2',
            'starting_cash' => 200000, // Drawer kasir 1 (POS-A) Rp 200.000
            'cash_sales' => 520000,
            'non_cash_sales' => 300000,
            'status' => 'open',
            'opened_at' => $today->copy()->setTime(15, 0, 0),
            'notes' => 'Laci kasir POS-A',
        ]);

        $this->seedOrdersForShift($nikitaShift, $users['nikita'], [
            ['customer' => 'Kak Dimas', 'time' => '16:00', 'pay_type' => 'cash', 'amount' => 520000],
            ['customer' => 'Meja 05', 'time' => '17:20', 'pay_type' => 'qris', 'amount' => 300000],
        ], $menus);

        // -------------------------------------------------------------
        // SKENARIO 3: DERY (Shift 2: 15:00 - 23:00 - OPEN / Kasir B)
        // Target: Kas Awal: Rp200.000, Penjualan Berjalan: Rp690.000
        // -------------------------------------------------------------
        $deryShift = CashierShift::create([
            'user_id' => $users['dery']->id_user,
            'shift_type' => 'shift_2',
            'starting_cash' => 200000, // Drawer kasir 2 (POS-B) Rp 200.000
            'cash_sales' => 450000,
            'non_cash_sales' => 240000,
            'status' => 'open',
            'opened_at' => $today->copy()->setTime(15, 5, 0),
            'notes' => 'Laci kasir POS-B (Takeaway bar)',
        ]);

        $this->seedOrdersForShift($deryShift, $users['dery'], [
            ['customer' => 'Rian (Grab/Takeaway)', 'time' => '15:30', 'pay_type' => 'cash', 'amount' => 450000],
            ['customer' => 'Meja 08', 'time' => '16:45', 'pay_type' => 'qris', 'amount' => 240000],
        ], $menus);
    }

    private function seedOrdersForShift(CashierShift $shift, User $cashier, array $orderList, $menus): void
    {
        foreach ($orderList as $data) {
            $orderTime = Carbon::today()->setTimeFromTimeString($data['time']);
            $order = Order::create([
                'tanggal' => $orderTime,
                'nama_pelanggan' => $data['customer'],
                'status_pembayaran' => 'paid',
                'status_order' => 'completed',
                'payment_method' => $data['pay_type'],
                'service_type' => 'dine_in',
                'id_user' => $cashier->id_user,
                'id_shift' => $shift->id_shift,
                'cashier_name' => $cashier->name,
                'subtotal' => $data['amount'],
                'total_harga' => $data['amount'],
                'final_total' => $data['amount'],
            ]);

            if ($menus->isNotEmpty()) {
                $menu = $menus->first();
                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_menu' => $menu->id_menu,
                    'quantity' => 1,
                    'subtotal' => $data['amount'],
                ]);
            }
        }
    }
}
