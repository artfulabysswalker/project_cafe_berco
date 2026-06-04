<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\QrisTransaction;
use Illuminate\Console\Command;

class TestQrisWebhook extends Command
{
    protected $signature = 'qris:test-webhook {order_id : Order ID to simulate payment for}';

    protected $description = 'Simulate Xendit webhook callback for testing';

    public function handle()
    {
        $orderId = $this->argument('order_id');

        $this->info("🧪 Simulating QRIS Webhook Callback for Order #{$orderId}...");
        $this->line('');

        // Find the order
        $order = Order::find($orderId);

        if (!$order) {
            $this->error("❌ Order #{$orderId} not found");
            return 1;
        }

        $this->info("✅ Order found: {$order->nama_pelanggan}");

        // Find or create QRIS transaction
        $qrisTransaction = QrisTransaction::where('id_order', $orderId)->first();

        if (!$qrisTransaction) {
            $this->error("❌ QRIS transaction not found for order #{$orderId}");
            $this->line('   Please create QRIS invoice first: php artisan qris:test {$orderId}');
            return 1;
        }

        $this->info("✅ QRIS Transaction found: #{$qrisTransaction->id_qris_transaction}");

        try {
            // Simulate webhook payload
            $webhookPayload = [
                'id' => $qrisTransaction->invoice_id,
                'external_id' => 'QRIS-ORDER-' . $orderId . '-' . time(),
                'status' => 'PAID',
                'amount' => (int) $qrisTransaction->amount,
                'currency' => 'IDR',
                'description' => "Order #{$orderId} - {$order->nama_pelanggan}",
                'paid_at' => now()->toIso8601String(),
            ];

            $this->line('');
            $this->info('📤 Webhook Payload:');
            $this->line(json_encode($webhookPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->line('');

            // Mark as paid
            $qrisTransaction->markAsPaid($webhookPayload['id']);

            $this->info("✅ QRIS Transaction marked as PAID");

            // Update reconciliation
            $reconciliation = $qrisTransaction->reconciliation;
            if ($reconciliation) {
                $reconciliation->update([
                    'bank_amount' => $qrisTransaction->amount,
                    'bank_transaction_date' => now(),
                ]);

                if ($reconciliation->amountsMatch()) {
                    $reconciliation->markAsMatched(auth()->id());
                    $this->info("✅ Reconciliation: MATCHED");
                } else {
                    $this->warn("⚠️ Reconciliation: MISMATCH");
                }
            }

            // Verify order status
            $order->refresh();
            $this->line('');
            $this->info('📋 Updated Order Status:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Order ID', "#$order->id_order"],
                    ['Payment Status', $order->status_pembayaran],
                    ['Order Status', $order->status_order],
                    ['Total Amount', 'Rp ' . number_format($order->total_harga, 0)],
                ]
            );

            $this->line('');
            $this->info('✅ Webhook simulation completed successfully!');

            $this->line('');
            $this->info('🔍 Verification:');
            $this->line('   Check QRIS transaction: php artisan qris:check ' . $orderId);
            $this->line('   Check order receipt: http://localhost:8000/order/' . $orderId . '/receipt');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error simulating webhook: ' . $e->getMessage());
            \Log::error('Test Webhook Error', ['error' => $e->getMessage()]);
            return 1;
        }
    }
}
