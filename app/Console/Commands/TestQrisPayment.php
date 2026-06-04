<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Models\Menu;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestQrisPayment extends Command
{
    protected $signature = 'qris:test {--user-id=1 : User ID for test order}';

    protected $description = 'Create a test order and QRIS transaction for testing';

    public function handle()
    {
        $this->info('🧪 Starting QRIS Payment Test...');
        $this->line('');

        // Step 1: Get or create test user
        $userId = $this->option('user-id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("❌ User with ID $userId not found");
            return 1;
        }

        $this->info("✅ User found: {$user->name} ({$user->email})");

        // Step 2: Get a menu item
        $menu = Menu::first();

        if (!$menu) {
            $this->error('❌ No menu items found in database');
            return 1;
        }

        $this->info("✅ Menu item found: {$menu->nama_menu} (Rp " . number_format($menu->harga, 0) . ")");

        // Step 3: Create test order
        $subtotal = $menu->harga * 2;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        try {
            $order = Order::create([
                'tanggal' => now(),
                'nama_pelanggan' => $user->name,
                'total_harga' => $total,
                'status_pembayaran' => 'pending',
                'service_type' => 'dine_in',
                'payment_method' => 'qris',
                'notes' => 'Test QRIS Payment',
                'status_order' => 'pending',
                'id_user' => $user->id_user,
            ]);

            $this->info("✅ Order created: #{$order->id_order}");

            // Add order items
            OrderItem::create([
                'id_order' => $order->id_order,
                'id_menu' => $menu->id_menu,
                'quantity' => 2,
                'subtotal' => $menu->harga * 2,
            ]);

            $this->info("✅ Order item added: {$menu->nama_menu} x2");

            // Step 4: Display order summary
            $this->line('');
            $this->info('📋 Order Summary:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Order ID', "#$order->id_order"],
                    ['Customer', $user->name],
                    ['Menu Item', $menu->nama_menu],
                    ['Quantity', '2'],
                    ['Unit Price', 'Rp ' . number_format($menu->harga, 0)],
                    ['Subtotal', 'Rp ' . number_format($subtotal, 0)],
                    ['Tax (10%)', 'Rp ' . number_format($tax, 0)],
                    ['Total', 'Rp ' . number_format($total, 0)],
                    ['Payment Method', 'QRIS'],
                    ['Status', 'Pending'],
                ]
            );

            // Step 5: Display next steps
            $this->line('');
            $this->info('🚀 Next Steps for Testing:');
            $this->line('');
            $this->line('1️⃣ Create QRIS Invoice:');
            $this->line("   <fg=green>POST http://localhost:8000/xendit/qris/payment/{$order->id_order}/invoice</>");
            $this->line('');

            $this->line('2️⃣ Check Payment Status:');
            $this->line("   <fg=green>GET http://localhost:8000/xendit/qris/payment/{$order->id_order}/status</>");
            $this->line('');

            $this->line('3️⃣ Simulate Webhook Callback (Test Payment):');
            $this->line("   <fg=yellow>php artisan qris:test-webhook {$order->id_order}</>");
            $this->line('');

            $this->line('4️⃣ Check Reconciliation:');
            $this->line("   <fg=yellow>php artisan qris:check {$order->id_order}</>");
            $this->line('');

            $this->info('✅ Test order created successfully!');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error creating test order: ' . $e->getMessage());
            return 1;
        }
    }
}
