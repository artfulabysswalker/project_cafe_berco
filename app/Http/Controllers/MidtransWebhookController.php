<?php

namespace App\Http\Controllers;

use App\Events\PaymentReceived;
use App\Models\PaymentLog;
use App\Services\QrisPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(private readonly QrisPaymentService $qrisService) {}

    /**
     * Terima notifikasi pembayaran dari Midtrans.
     *
     * Endpoint ini terbuka (tanpa auth & CSRF) sehingga signature wajib
     * diverifikasi sebelum status apa pun dianggap sah.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        Log::info('Notifikasi Midtrans diterima', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'status_code' => $payload['status_code'] ?? null,
        ]);

        if (! $this->qrisService->verifySignature($payload)) {
            return response()->json(['status' => 'invalid_signature'], 403);
        }

        $paymentLog = PaymentLog::where('midtrans_order_id', $payload['order_id'] ?? '')->first();

        if (! $paymentLog) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // Idempotensi: Midtrans boleh mengirim notifikasi yang sama
        // berkali-kali; pesanan yang sudah lunas tidak diproses ulang.
        if ($paymentLog->paid_at !== null) {
            return response()->json(['status' => 'OK'], 200);
        }

        if (! isset($payload['gross_amount'])
            || (float) $payload['gross_amount'] !== (float) $paymentLog->gross_amount) {
            Log::warning('Notifikasi Midtrans ditolak: nominal tidak cocok', [
                'payment_log_id' => $paymentLog->getKey(),
                'expected' => $paymentLog->gross_amount,
                'payload' => $payload['gross_amount'] ?? null,
            ]);

            return response()->json(['status' => 'amount_mismatch'], 400);
        }

        $paymentLog = $this->qrisService->applyStatus($paymentLog, $payload);

        if ($paymentLog->isPaid() && $paymentLog->order) {
            event(new PaymentReceived($paymentLog->order, $paymentLog));
        }

        return response()->json(['status' => 'OK'], 200);
    }
}
