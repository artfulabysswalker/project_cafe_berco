<?php

namespace App\Services;

use App\Models\Order;
use App\Models\QrisTransaction;
use App\Models\QrisReconciliation;
use Exception;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\CustomerObject;
use Xendit\Invoice\InvoiceItem;

class QrisPaymentService
{
    protected $invoiceApi;

    public function __construct()
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));
        $this->invoiceApi = new InvoiceApi();
    }

    /**
     * Create QRIS payment invoice for order
     */
    public function createQrisInvoice(Order $order): array
    {
        try {
            // Create customer object
            $customer = new CustomerObject([
                'given_names' => $order->nama_pelanggan,
                'email' => $order->user?->email ?? 'customer@bercocafe.com',
                'mobile_number' => $order->user?->phone ?? '08000000000',
            ]);

            // Prepare items
            $items = $this->prepareInvoiceItems($order);

            // Create invoice request - QRIS only
            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => 'QRIS-ORDER-' . $order->id_order . '-' . time(),
                'amount' => (int) $order->total_harga,
                'description' => 'Order #' . $order->id_order . ' - ' . $order->nama_pelanggan,
                'customer' => $customer,
                'items' => $items,
                'due_date' => now()->addMinutes(30)->toIso8601String(),
                'payment_methods' => ['QRIS'], // QRIS only
                'success_redirect_url' => route('xendit.qris.success', ['order' => $order->id_order]),
                'failure_redirect_url' => route('xendit.qris.failed', ['order' => $order->id_order]),
                'currency' => 'IDR',
            ]);

            // Create invoice via Xendit
            $invoice = $this->invoiceApi->createInvoice($createInvoiceRequest);

            \Log::info('Xendit Invoice API Response', [
                'invoice_keys' => array_keys((array)$invoice),
                'full_response' => (array)$invoice,
            ]);

            // Create QRIS transaction record
            $qrisTransaction = QrisTransaction::create([
                'id_order' => $order->id_order,
                'qris_code' => $invoice['qris_string'] ?? $invoice['id'] ?? null,
                'transaction_id' => $invoice['id'],
                'invoice_id' => $invoice['id'],
                'amount' => $order->total_harga,
                'status' => 'pending',
                'payment_channel' => 'qris',
                'customer_name' => $order->nama_pelanggan,
                'customer_email' => $order->user?->email,
                'customer_phone' => $order->user?->phone,
                'expires_at' => now()->addMinutes(30),
                'metadata' => [
                    'external_id' => $createInvoiceRequest->getExternalId(),
                    'invoice_url' => $invoice['invoice_url'] ?? null,
                    'qris_string' => $invoice['qris_string'] ?? null,
                ],
            ]);

            // Create reconciliation record
            QrisReconciliation::create([
                'id_qris_transaction' => $qrisTransaction->id_qris_transaction,
                'system_amount' => $order->total_harga,
            ]);

            return [
                'success' => true,
                'invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url'],
                'qris_string' => $invoice['qris_string'] ?? null,
                'order_id' => $order->id_order,
                'amount' => $order->total_harga,
                'expires_at' => now()->addMinutes(30)->toIso8601String(),
            ];
        } catch (Exception $e) {
            \Log::error('Xendit QRIS Invoice Creation Error', [
                'order_id' => $order->id_order,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Prepare invoice items from order
     */
    private function prepareInvoiceItems(Order $order): array
    {
        $items = [];

        foreach ($order->orderItems as $item) {
            $invoiceItem = new InvoiceItem([
                'name' => $item->menu->nama_menu,
                'quantity' => $item->quantity,
                'price' => (int) $item->menu->harga,
                'category' => 'menu',
            ]);
            $items[] = $invoiceItem;
        }

        // Add tax as item
        $subtotal = $order->orderItems->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });
        $tax = $subtotal * 0.1;

        if ($tax > 0) {
            $taxItem = new InvoiceItem([
                'name' => 'PPN 10%',
                'quantity' => 1,
                'price' => (int) $tax,
                'category' => 'tax',
            ]);
            $items[] = $taxItem;
        }

        return $items;
    }

    /**
     * Process webhook callback from Xendit
     */
    public function processCallback(array $data): array
    {
        try {
            if (!isset($data['external_id'])) {
                throw new Exception('Invalid callback data: missing external_id');
            }

            $externalId = $data['external_id'];
            
            // Extract order ID from external_id (format: QRIS-ORDER-{id}-{timestamp})
            $parts = explode('-', $externalId);
            if (count($parts) < 3) {
                throw new Exception('Invalid external_id format');
            }

            $orderId = $parts[2];
            $order = Order::where('id_order', $orderId)->first();

            if (!$order) {
                throw new Exception("Order #$orderId not found");
            }

            $qrisTransaction = QrisTransaction::where('id_order', $orderId)->first();

            if (!$qrisTransaction) {
                throw new Exception("QRIS transaction for order #$orderId not found");
            }

            // Update transaction status based on Xendit response
            $status = $data['status'] ?? null;

            switch ($status) {
                case 'PAID':
                case 'SETTLED':
                    $qrisTransaction->markAsPaid($data['id'] ?? null);
                    $this->reconcilePayment($qrisTransaction, $data);
                    $this->sendPaymentNotification($order, $qrisTransaction);
                    break;

                case 'EXPIRED':
                case 'FAILED':
                    $qrisTransaction->markAsFailed($data['failure_code'] ?? $status);
                    break;

                case 'PENDING':
                default:
                    $qrisTransaction->update(['status' => 'pending']);
                    break;
            }

            \Log::info('QRIS Callback Processed', [
                'order_id' => $order->id_order,
                'status' => $status,
                'transaction_id' => $qrisTransaction->id_qris_transaction,
            ]);

            return ['success' => true, 'message' => 'Callback processed successfully'];
        } catch (Exception $e) {
            \Log::error('QRIS Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Reconcile payment after successful payment
     */
    private function reconcilePayment(QrisTransaction $transaction, array $xenditData): void
    {
        $reconciliation = $transaction->reconciliation;

        if (!$reconciliation) {
            return;
        }

        // Update bank amount from Xendit
        $bankAmount = $xenditData['amount'] ?? $transaction->amount;

        $reconciliation->update([
            'bank_amount' => $bankAmount,
            'bank_transaction_date' => now(),
        ]);

        // Check if amounts match
        if ($reconciliation->amountsMatch()) {
            $reconciliation->markAsMatched();
        } else {
            $difference = abs($bankAmount - $transaction->amount);
            $reconciliation->markAsMismatched($difference, "Amount mismatch: Bank=$bankAmount, System={$transaction->amount}");
        }
    }

    /**
     * Send payment confirmation notification
     */
    private function sendPaymentNotification(Order $order, QrisTransaction $transaction): void
    {
        try {
            if ($order->user) {
                $order->user->notify(new \App\Notifications\PaymentConfirmationNotification($order, $transaction));
            }
        } catch (Exception $e) {
            \Log::warning('Failed to send payment notification', [
                'order_id' => $order->id_order,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check payment status from Xendit
     */
    public function checkPaymentStatus(Order $order): array
    {
        try {
            $qrisTransaction = QrisTransaction::where('id_order', $order->id_order)->first();

            if (!$qrisTransaction) {
                return [
                    'success' => false,
                    'message' => 'QRIS transaction not found',
                ];
            }

            // If expired and still pending, mark as expired
            if ($qrisTransaction->isExpired()) {
                $qrisTransaction->update(['status' => 'expired']);
            }

            return [
                'success' => true,
                'status' => $qrisTransaction->status,
                'amount' => $qrisTransaction->amount,
                'transaction_id' => $qrisTransaction->transaction_id,
                'paid_at' => $qrisTransaction->paid_at,
                'expires_at' => $qrisTransaction->expires_at,
            ];
        } catch (Exception $e) {
            \Log::error('Check QRIS Status Error', [
                'order_id' => $order->id_order,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage(),
            ];
        }
    }
}
