<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use App\Models\Order;
use App\Models\Payment;
use Exception;

class XenditPaymentService
{
    /**
     * Initialize Xendit with API key
     */
    public static function init()
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));
    }

    /**
     * Create invoice for order
     */
    public static function createInvoice(Order $order, array $options = [])
    {
        try {
            self::init();

            $apiInstance = new InvoiceApi();
            
            $invoiceData = [
                'external_id' => 'ORDER-' . $order->id_order . '-' . time(),
                'amount' => (int) $order->total_harga,
                'description' => $options['description'] ?? 'Order #' . $order->id_order . ' - ' . $order->nama_pelanggan,
                'payment_methods' => $options['payment_methods'] ?? self::getPaymentMethods($order->payment_method),
                'currency' => 'IDR',
            ];

            $createInvoiceRequest = new CreateInvoiceRequest($invoiceData);
            $invoice = $apiInstance->createInvoice($createInvoiceRequest);

            // Save to database
            Payment::updateOrCreate(
                ['id_order' => $order->id_order],
                [
                    'snap_token' => $invoice->getId(),
                    'payment_method' => $order->payment_method,
                    'metode_pembayaran' => $order->payment_method,
                    'amount' => $order->total_harga,
                    'jumlah_bayar' => (int) $order->total_harga,
                    'status' => 'pending',
                    'transaction_id' => $invoice->getId(),
                    'tanggal_bayar' => now(),
                ]
            );

            return $invoice;
        } catch (Exception $e) {
            throw new Exception('Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get payment status from Xendit
     */
    public static function getInvoiceStatus($invoiceId)
    {
        try {
            self::init();
            $apiInstance = new InvoiceApi();
            $invoice = $apiInstance->getInvoiceById($invoiceId);
            return $invoice;
        } catch (Exception $e) {
            throw new Exception('Failed to get invoice status: ' . $e->getMessage());
        }
    }

    /**
     * Check order payment status
     */
    public static function checkOrderPaymentStatus(Order $order)
    {
        try {
            $payment = Payment::where('id_order', $order->id_order)->first();

            if (!$payment || !$payment->transaction_id) {
                return [
                    'status' => 'no_payment',
                    'message' => 'No payment record found',
                ];
            }

            $invoice = self::getInvoiceStatus($payment->transaction_id);

            return [
                'status' => $invoice['status'],
                'invoice_id' => $invoice['id'],
                'amount' => $invoice['amount'],
                'paid_amount' => $invoice['paid_amount'] ?? 0,
                'created' => $invoice['created'] ?? null,
                'updated' => $invoice['updated'] ?? null,
                'payment_record' => [
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'created_at' => $payment->created_at,
                ]
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to check payment status: ' . $e->getMessage());
        }
    }

    /**
     * Get available payment methods
     */
    public static function getPaymentMethods($paymentType = null)
    {
        $methods = [];

        if (!$paymentType) {
            return ['BANK_TRANSFER', 'DEBIT_CARD', 'CREDIT_CARD', 'OVO', 'DANA', 'LINKAJA', 'ASTRAPAY'];
        }

        switch ($paymentType) {
            case 'card':
                $methods = ['CREDIT_CARD', 'DEBIT_CARD'];
                break;
            case 'e_wallet':
                $methods = ['OVO', 'DANA', 'LINKAJA', 'ASTRAPAY'];
                break;
            case 'bank_transfer':
                $methods = ['BANK_TRANSFER'];
                break;
            case 'retail':
                $methods = ['RETAIL'];
                break;
            default:
                $methods = ['BANK_TRANSFER', 'DEBIT_CARD', 'CREDIT_CARD', 'OVO', 'DANA', 'LINKAJA'];
        }

        return $methods;
    }

    /**
     * Parse webhook callback
     */
    public static function parseWebhookCallback(array $data)
    {
        return [
            'invoice_id' => $data['id'] ?? null,
            'external_id' => $data['external_id'] ?? null,
            'status' => $data['status'] ?? null,
            'amount' => $data['amount'] ?? null,
            'paid_amount' => $data['paid_amount'] ?? 0,
            'payer_email' => $data['payer_email'] ?? null,
            'description' => $data['description'] ?? null,
            'created' => $data['created'] ?? null,
            'updated' => $data['updated'] ?? null,
        ];
    }

    /**
     * Test invoice creation (for testing purposes)
     */
    public static function createTestInvoice($amount = 100000, $description = 'Test Invoice')
    {
        try {
            self::init();

            $apiInstance = new InvoiceApi();
            
            $invoiceData = [
                'external_id' => 'TEST-' . time() . '-' . rand(1000, 9999),
                'amount' => (int) $amount,
                'description' => $description,
                'payment_methods' => self::getPaymentMethods(),
                'currency' => 'IDR',
            ];

            $createInvoiceRequest = new CreateInvoiceRequest($invoiceData);
            return $apiInstance->createInvoice($createInvoiceRequest);
        } catch (Exception $e) {
            throw new Exception('Failed to create test invoice: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status from webhook
     */
    public static function updatePaymentFromWebhook(array $webhookData)
    {
        try {
            $externalId = $webhookData['external_id'] ?? null;
            if (!$externalId) {
                throw new Exception('Invalid webhook data: external_id missing');
            }

            // Extract order ID from external_id format: ORDER-{id}-{timestamp}
            $parts = explode('-', $externalId);
            if (count($parts) < 2) {
                throw new Exception('Invalid external_id format');
            }

            $orderId = $parts[1];
            $order = Order::where('id_order', $orderId)->first();

            if (!$order) {
                throw new Exception('Order not found: ' . $orderId);
            }

            $payment = Payment::where('id_order', $order->id_order)->first();
            if (!$payment) {
                throw new Exception('Payment record not found for order: ' . $orderId);
            }

            $status = $webhookData['status'] ?? null;

            // Update payment status
            switch ($status) {
                case 'PAID':
                case 'SETTLED':
                    $payment->update([
                        'status' => 'paid',
                        'transaction_id' => $webhookData['id'] ?? null,
                    ]);
                    $order->update(['status_pembayaran' => 'Paid']);
                    break;

                case 'PENDING':
                    $payment->update([
                        'status' => 'pending',
                        'transaction_id' => $webhookData['id'] ?? null,
                    ]);
                    break;

                case 'EXPIRED':
                case 'FAILED':
                    $payment->update([
                        'status' => 'failed',
                        'transaction_id' => $webhookData['id'] ?? null,
                    ]);
                    $order->update(['status_pembayaran' => 'Failed']);
                    break;

                case 'VOIDED':
                    $payment->update(['status' => 'voided']);
                    break;
            }

            return [
                'success' => true,
                'order_id' => $order->id_order,
                'payment_status' => $payment->status,
                'order_status' => $order->status_pembayaran,
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to update payment: ' . $e->getMessage());
        }
    }
}
