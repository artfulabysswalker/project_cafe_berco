<?php

namespace App\Console\Commands;

use App\Models\QrisTransaction;
use Illuminate\Console\Command;

class CheckQrisStatus extends Command
{
    protected $signature = 'qris:check {order_id? : Order ID to check}';

    protected $description = 'Check QRIS payment status';

    public function handle()
    {
        $orderId = $this->argument('order_id');

        if ($orderId) {
            // Check single order
            $transaction = QrisTransaction::where('id_order', $orderId)->first();

            if (!$transaction) {
                $this->error("❌ No QRIS transaction found for order #{$orderId}");
                return 1;
            }

            $this->displayTransaction($transaction);
        } else {
            // Show pending transactions
            $pendingTransactions = QrisTransaction::where('status', 'pending')->get();

            if ($pendingTransactions->isEmpty()) {
                $this->info('✅ No pending transactions');
                return 0;
            }

            $this->info("📊 Pending Transactions: {$pendingTransactions->count()}");
            $this->line('');

            foreach ($pendingTransactions as $transaction) {
                $this->displayTransaction($transaction);
                $this->line('---');
            }
        }

        return 0;
    }

    private function displayTransaction(QrisTransaction $transaction)
    {
        $this->line("Transaction ID: #{$transaction->id_qris_transaction}");
        $this->line("Order ID: #{$transaction->id_order}");
        $this->line("Amount: Rp " . number_format($transaction->amount, 0));
        $this->line("Status: <fg=" . $this->getStatusColor($transaction->status) . ">{$transaction->status}</>");
        $this->line("Payment Channel: {$transaction->payment_channel}");
        $this->line("Customer: {$transaction->customer_name} ({$transaction->customer_email})");
        $this->line("Created: {$transaction->created_at->format('Y-m-d H:i:s')}");
        $this->line("Expires: {$transaction->expires_at?->format('Y-m-d H:i:s') ?? 'N/A'}");

        if ($transaction->paid_at) {
            $this->line("Paid: {$transaction->paid_at->format('Y-m-d H:i:s')}");
        }

        if ($transaction->isExpired()) {
            $this->warn("⚠️ This transaction has expired!");
        }
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'expired' => 'red',
            'cancelled' => 'red',
            default => 'white',
        };
    }
}
