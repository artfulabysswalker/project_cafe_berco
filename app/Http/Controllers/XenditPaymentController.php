<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\CustomerObject;
use Xendit\Invoice\InvoiceItem;
use Exception;

class XenditPaymentController extends Controller
{
    public function __construct()
    {
        // Set Xendit API Key
        Configuration::setXenditKey(config('services.xendit.secret_key'));
    }

    /**
     * Create Xendit Invoice for payment
     */
    public function createInvoice(Order $order)
    {
        try {
            $invoiceApi = new InvoiceApi();

            // Create customer object
            $customer = new CustomerObject([
                'given_names' => $order->nama_pelanggan,
                'email' => auth()->user()->email ?? 'customer@bercocafe.com',
                'mobile_number' => auth()->user()->phone ?? '08000000000',
            ]);

            // Prepare items
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

            // Create invoice request
            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => 'ORDER-' . $order->id_order . '-' . time(),
                'amount' => (int) $order->total_harga,
                'description' => 'Order #' . $order->id_order . ' - ' . $order->nama_pelanggan,
                'customer' => $customer,
                'items' => $items,
                'due_date' => now()->addMinutes(30)->toIso8601String(),
                'payment_methods' => $this->getPaymentMethods($order->payment_method),
                'success_redirect_url' => route('payment.success', ['order' => $order->id_order]),
                'failure_redirect_url' => route('payment.failed', ['order' => $order->id_order]),
                'currency' => 'IDR',
            ]);

            // Create invoice via Xendit
            $invoice = $invoiceApi->createInvoice($createInvoiceRequest);

            // Update or create payment record
            Payment::updateOrCreate(
                ['id_order' => $order->id_order],
                [
                    'snap_token' => $invoice['id'],
                    'payment_method' => $order->payment_method,
                    'metode_pembayaran' => $order->payment_method,
                    'amount' => $order->total_harga,
                    'jumlah_bayar' => (int) $order->total_harga,
                    'status' => 'pending',
                    'transaction_id' => $invoice['id'],
                    'tanggal_bayar' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url'],
                'order_id' => $order->id_order,
                'amount' => $order->total_harga,
            ]);
        } catch (Exception $e) {
            \Log::error('Xendit Invoice Creation Error', [
                'order_id' => $order->id_order,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Get payment methods based on payment type
     */
    private function getPaymentMethods($paymentMethod)
    {
        switch ($paymentMethod) {
            case 'card':
                return ['CREDIT_CARD', 'DEBIT_CARD'];
            case 'e_wallet':
                return ['OVO', 'DANA', 'LINKAJA', 'ASTRAPAY', 'GOPAY'];
            case 'bank_transfer':
                // Use specific bank codes that are activated in Xendit dashboard
                return ['BNI', 'BRI', 'MANDIRI', 'PERMATA'];
            case 'retail':
                return ['RETAIL'];
            default:
                return ['CREDIT_CARD', 'BNI', 'OVO', 'DANA', 'LINKAJA', 'GOPAY'];
        }
    }

    /**
     * Redirect to Xendit payment page
     */
    public function redirectToPayment(Order $order)
    {
        try {
            // Create invoice first
            $invoiceResponse = $this->createInvoice($order);
            $invoiceData = json_decode($invoiceResponse->content(), true);

            if (!$invoiceData['success']) {
                return redirect()->route('payment.show', ['order' => $order->id_order])
                    ->with('error', $invoiceData['message']);
            }

            // Redirect to Xendit payment page
            return redirect()->away($invoiceData['invoice_url']);
        } catch (Exception $e) {
            return redirect()->route('payment.show', ['order' => $order->id_order])
                ->with('error', 'Gagal redirect ke pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Handle Xendit webhook/callback
     */
    public function callback(Request $request)
    {
        try {
            $data = $request->all();

            \Log::info('Xendit Callback Received', $data);

            // Find order by external_id
            if (!isset($data['external_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid callback data',
                ], 400);
            }

            $externalId = $data['external_id'];
            $orderId = explode('-', $externalId)[1] ?? null;

            $order = Order::where('id_order', $orderId)->first();
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $payment = Payment::where('id_order', $order->id_order)->first();
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found',
                ], 404);
            }

            // Update payment status based on Xendit response
            $status = $data['status'] ?? null;

            if ($status === 'PAID' || $status === 'SETTLED') {
                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => $data['id'] ?? null,
                ]);

                $order->update([
                    'status_pembayaran' => 'Paid',
                ]);

                // Send payment confirmation email
                if ($order->user) {
                    $order->user->notify(new \App\Notifications\PaymentConfirmationNotification($order, $payment));
                }

                \Log::info('Payment marked as PAID', ['order_id' => $order->id_order]);
            } elseif ($status === 'PENDING') {
                $payment->update([
                    'status' => 'pending',
                    'transaction_id' => $data['id'] ?? null,
                ]);
            } elseif ($status === 'EXPIRED' || $status === 'FAILED') {
                $payment->update([
                    'status' => 'failed',
                    'transaction_id' => $data['id'] ?? null,
                ]);

                $order->update([
                    'status_pembayaran' => 'Failed',
                ]);

                \Log::warning('Payment marked as FAILED/EXPIRED', ['order_id' => $order->id_order]);
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            \Log::error('Xendit Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing callback: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status from Xendit
     */
    public function checkStatus(Order $order)
    {
        try {
            $payment = Payment::where('id_order', $order->id_order)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'payment_status' => $payment->status,
                'order_status' => $order->status_pembayaran,
                'amount' => $payment->amount,
                'transaction_id' => $payment->transaction_id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Success callback from Xendit
     */
    public function success(Order $order)
    {
        try {
            $payment = Payment::where('id_order', $order->id_order)->first();

            if ($payment && $payment->status === 'paid') {
                return redirect()->route('order.receipt', $order)->with('success', 'Pembayaran berhasil!');
            }

            return redirect()->route('order.receipt', $order)->with('info', 'Pembayaran sedang diproses');
        } catch (Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Failed callback from Xendit
     */
    public function failed(Order $order)
    {
        try {
            $payment = Payment::where('id_order', $order->id_order)->first();

            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            return redirect()->route('cart.index')->with('error', 'Pembayaran gagal. Silahkan coba lagi.');
        } catch (Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
