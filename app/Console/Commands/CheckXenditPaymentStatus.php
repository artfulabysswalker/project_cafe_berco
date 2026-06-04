<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Services\XenditPaymentService;
use Exception;

class CheckXenditPaymentStatus extends Command
{
    protected $signature = 'xendit:check-payment {order_id : Order ID to check}';
    protected $description = 'Check payment status for a specific order';

    public function handle()
    {
        try {
            $orderId = $this->argument('order_id');

            $this->info('Checking payment status for Order #' . $orderId);
            $this->newLine();

            $payment = Payment::where('id_order', $orderId)->first();

            if (!$payment) {
                $this->error('Payment record not found for Order #' . $orderId);
                return self::FAILURE;
            }

            $this->table(
                ['Field', 'Value'],
                [
                    ['Order ID', $orderId],
                    ['Payment ID', $payment->id_payment],
                    ['Invoice ID', $payment->transaction_id],
                    ['Status', $payment->status],
                    ['Amount', 'Rp ' . number_format($payment->amount, 0, ',', '.')],
                    ['Created', $payment->created_at],
                    ['Updated', $payment->updated_at],
                ]
            );

            if ($payment->transaction_id) {
                $this->newLine();
                $this->info('Fetching live status from Xendit...');

                try {
                    $status = XenditPaymentService::getInvoiceStatus($payment->transaction_id);

                    $this->table(
                        ['Field', 'Value'],
                        [
                            ['Xendit Invoice ID', $status['id']],
                            ['Xendit Status', $status['status']],
                            ['Amount', 'Rp ' . number_format($status['amount'], 0, ',', '.')],
                            ['Paid Amount', 'Rp ' . number_format($status['paid_amount'] ?? 0, 0, ',', '.')],
                            ['Created', $status['created'] ?? 'N/A'],
                            ['Updated', $status['updated'] ?? 'N/A'],
                        ]
                    );

                    if ($status['status'] !== $payment->status) {
                        $this->warn('⚠️  Status mismatch detected!');
                        $this->line('Local: ' . $payment->status);
                        $this->line('Xendit: ' . $status['status']);
                    } else {
                        $this->info('✓ Status is in sync');
                    }
                } catch (Exception $e) {
                    $this->error('Failed to fetch Xendit status: ' . $e->getMessage());
                }
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
