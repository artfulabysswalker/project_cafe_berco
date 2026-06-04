<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\QrisTransaction;
use App\Services\QrisPaymentService;
use Illuminate\Http\Request;
use Exception;

class QrisPaymentController extends Controller
{
    protected $qrisService;

    public function __construct(QrisPaymentService $qrisService)
    {
        $this->qrisService = $qrisService;
    }

    /**
     * Create QRIS invoice and redirect to payment
     */
    public function createInvoice(Order $order)
    {
        try {
            $result = $this->qrisService->createQrisInvoice($order);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redirect to Xendit QRIS payment page
     */
    public function redirectToPayment(Order $order)
    {
        try {
            $invoiceResponse = $this->createInvoice($order);
            $invoiceData = json_decode($invoiceResponse->content(), true);

            if (!$invoiceData['success']) {
                return redirect()->route('xendit.qris.show', ['order' => $order->id_order])
                    ->with('error', $invoiceData['message']);
            }

            // Redirect to Xendit payment page
            return redirect()->away($invoiceData['invoice_url']);
        } catch (Exception $e) {
            return redirect()->route('xendit.qris.show', ['order' => $order->id_order])
                ->with('error', 'Gagal redirect ke pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Show QRIS payment page
     */
    public function show(Order $order)
    {
        try {
            $qrisTransaction = QrisTransaction::where('id_order', $order->id_order)->first();

            $subtotal = $order->orderItems->sum(function ($item) {
                return $item->menu->harga * $item->quantity;
            });

            $tax = $subtotal * 0.1;

            return view('payment.qris', [
                'order' => $order,
                'qrisTransaction' => $qrisTransaction,
                'subtotal' => $subtotal,
                'tax' => $tax,
            ]);
        } catch (Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Check QRIS payment status
     */
    public function checkStatus(Order $order)
    {
        try {
            $result = $this->qrisService->checkPaymentStatus($order);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle QRIS webhook callback from Xendit
     */
    public function callback(Request $request)
    {
        try {
            $data = $request->all();

            \Log::info('QRIS Callback Received', [
                'external_id' => $data['external_id'] ?? 'N/A',
                'status' => $data['status'] ?? 'N/A',
            ]);

            $result = $this->qrisService->processCallback($data);

            return response()->json($result);
        } catch (Exception $e) {
            \Log::error('QRIS Callback Processing Error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing callback: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Success redirect from Xendit
     */
    public function success(Order $order)
    {
        try {
            $qrisTransaction = QrisTransaction::where('id_order', $order->id_order)->first();

            if ($qrisTransaction) {
                // If redirected from Xendit success page, mark as paid immediately
                if ($qrisTransaction->status !== 'paid') {
                    $qrisTransaction->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                    
                    // Update order status to paid
                    $order->update([
                        'payment_status' => 'paid',
                    ]);
                }

                return redirect()->route('order.receipt', $order)
                    ->with('success', '✅ Pembayaran QRIS berhasil! Terima kasih telah berbelanja.');
            }

            return redirect()->route('cart.index')
                ->with('error', 'Transaksi QRIS tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Failed redirect from Xendit
     */
    public function failed(Order $order)
    {
        try {
            $qrisTransaction = QrisTransaction::where('id_order', $order->id_order)->first();

            if ($qrisTransaction) {
                $qrisTransaction->markAsFailed('User cancelled payment');
            }

            return redirect()->route('xendit.qris.show', $order)
                ->with('error', '❌ Pembayaran QRIS dibatalkan atau gagal. Silahkan coba lagi.');
        } catch (Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
