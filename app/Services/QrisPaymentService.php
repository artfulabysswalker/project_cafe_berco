<?php

namespace App\Services;

use App\Exceptions\QrisChargeException;
use App\Models\Order;
use App\Models\PaymentLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use Throwable;

/**
 * Pembayaran QRIS via Midtrans Core API (sandbox untuk demo).
 *
 * Menggantikan alur Xendit Invoice QRIS: charge dibuat di backend,
 * gambar QR ditampilkan langsung ke pelanggan, dan status lunas hanya
 * ditentukan oleh notifikasi Midtrans yang tervalidasi signature.
 */
class QrisPaymentService
{
    /**
     * Masa berlaku QRIS bila Midtrans tidak mengirim expiry_time.
     */
    public const EXPIRY_MINUTES = 15;

    public function __construct()
    {
        $this->configure();
    }

    /**
     * Set kredensial & mode Midtrans dari konfigurasi aplikasi.
     */
    public function configure(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function isConfigured(): bool
    {
        return ! empty(config('services.midtrans.server_key'));
    }

    /**
     * Buat charge QRIS untuk pesanan dan simpan ke payment_logs.
     *
     * Charge yang masih pending dipakai ulang agar halaman pelanggan tidak
     * membuat transaksi ganda saat di-refresh.
     */
    public function createQrisCharge(Order $order): PaymentLog
    {
        if (! $this->isConfigured()) {
            throw new QrisChargeException('MIDTRANS_SERVER_KEY belum diatur di .env.');
        }

        $grossAmount = $this->grossAmount($order);

        if ($grossAmount <= 0) {
            throw new QrisChargeException('Total pesanan harus lebih besar dari nol untuk dibayar via QRIS.');
        }

        $existing = $order->paymentLogs()
            ->whereNull('paid_at')
            ->latest('id')
            ->first();

        if ($existing && $existing->qr_code_url && ! $existing->isExpired()) {
            return $existing;
        }

        $midtransOrderId = $this->buildMidtransOrderId($order);
        $payload = $this->buildChargePayload($order, $midtransOrderId, $grossAmount);

        try {
            $response = (array) CoreApi::charge($payload);
        } catch (Throwable $e) {
            Log::error('Midtrans QRIS charge gagal', [
                'order_id' => $order->id_order,
                'midtrans_order_id' => $midtransOrderId,
                'error' => $e->getMessage(),
            ]);

            throw new QrisChargeException('Gagal membuat QRIS: '.$e->getMessage(), previous: $e);
        }

        $expiredAt = $this->resolveExpiry($response);

        $log = PaymentLog::create([
            'order_id' => $order->id_order,
            'midtrans_order_id' => (string) ($response['order_id'] ?? $midtransOrderId),
            'transaction_id' => $response['transaction_id'] ?? null,
            'qr_code_url' => $this->extractQrCodeUrl($response),
            'gross_amount' => $grossAmount,
            'transaction_status' => (string) ($response['transaction_status'] ?? 'pending'),
            'fraud_status' => $response['fraud_status'] ?? null,
            'payment_type' => (string) ($response['payment_type'] ?? 'qris'),
            'raw_response' => $response,
            'expired_at' => $expiredAt,
        ]);

        Log::info('Midtrans QRIS charge dibuat', [
            'order_id' => $order->id_order,
            'midtrans_order_id' => $log->midtrans_order_id,
            'transaction_id' => $log->transaction_id,
            'gross_amount' => $grossAmount,
        ]);

        return $log;
    }

    /**
     * Verifikasi signature_key notifikasi Midtrans.
     *
     * signature = SHA512(order_id . status_code . gross_amount . serverKey)
     */
    public function verifySignature(array $payload, ?string $serverKey = null): bool
    {
        $serverKey = $serverKey ?? config('services.midtrans.server_key');

        $signature = $payload['signature_key'] ?? null;

        if (empty($signature) || empty($serverKey)) {
            return false;
        }

        if (! isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'])) {
            return false;
        }

        $expected = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey
        );

        return hash_equals($expected, (string) $signature);
    }

    /**
     * Ambil status transaksi terbaru langsung dari API Midtrans.
     *
     * Dipakai tombol "Cek Status" saat webhook belum terkait (mis. lokal
     * tanpa ngrok). Respons ini berasal dari server-to-server dengan server
     * key, sehingga dipercaya setelah amount dicocokkan di applyStatus().
     */
    public function fetchStatus(PaymentLog $log): ?array
    {
        if (! $this->isConfigured() || empty($log->midtrans_order_id)) {
            return null;
        }

        try {
            return (array) Transaction::status($log->midtrans_order_id);
        } catch (Throwable $e) {
            Log::warning('Gagal mengambil status transaksi Midtrans', [
                'payment_log_id' => $log->getKey(),
                'midtrans_order_id' => $log->midtrans_order_id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Terapkan payload status (webhook atau respons API) ke payment_logs
     * dan pesanan terkait, secara idempoten dan dalam satu transaksi DB.
     *
     * Lunas hanya bila tiga syarat terpenuhi: status_code 200,
     * fraud_status accept, dan transaction_status settlement/capture,
     * ditambah pencocokan nominal.
     */
    public function applyStatus(PaymentLog $log, array $payload): PaymentLog
    {
        return DB::transaction(function () use ($log, $payload) {
            $locked = PaymentLog::whereKey($log->getKey())->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($locked->order_id)->lockForUpdate()->first();

            $status = (string) ($payload['transaction_status'] ?? $locked->transaction_status);
            $amountMatches = ! isset($payload['gross_amount'])
                || (float) $payload['gross_amount'] === (float) $locked->gross_amount;

            $paid = $this->isPaidNotification($payload) && $amountMatches;
            $failed = in_array($status, ['expire', 'deny', 'cancel'], true);

            if (! $amountMatches && $this->isPaidNotification($payload)) {
                Log::warning('Notifikasi Midtrans diabaikan: nominal tidak cocok', [
                    'payment_log_id' => $locked->getKey(),
                    'expected' => $locked->gross_amount,
                    'payload' => $payload['gross_amount'] ?? null,
                ]);
            }

            $locked->transaction_status = $status;
            $locked->fraud_status = $payload['fraud_status'] ?? $locked->fraud_status;
            $locked->payment_type = $payload['payment_type'] ?? $locked->payment_type;
            $locked->transaction_id = $payload['transaction_id'] ?? $locked->transaction_id;
            $locked->signature_key = $payload['signature_key'] ?? $locked->signature_key;
            $locked->raw_response = $payload;

            if (! empty($payload['expiry_time'])) {
                $locked->expired_at = $this->parseMidtransTime($payload['expiry_time']) ?? $locked->expired_at;
            }

            if ($paid && $locked->paid_at === null) {
                $locked->paid_at = now();
            }

            $locked->save();

            if ($order) {
                $this->syncOrderStatus($order, $status, $paid, $failed);
            }

            return $locked->refresh();
        });
    }

    /**
     * Perbarui status pembayaran pesanan setelah log diperbarui.
     */
    protected function syncOrderStatus(Order $order, string $status, bool $paid, bool $failed): void
    {
        if ($order->isCancelled()) {
            Log::warning('Pembayaran untuk pesanan yang dibatalkan diabaikan', [
                'order_id' => $order->id_order,
                'transaction_status' => $status,
            ]);

            return;
        }

        if ($paid) {
            if ($order->isPaid()) {
                return;
            }

            $order->forceFill([
                'status_pembayaran' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => $order->paid_at ?? now(),
                'status_order' => in_array($order->status_order, ['pending', 'confirmed'], true)
                    ? 'processing'
                    : $order->status_order,
            ])->save();

            return;
        }

        if ($failed && ! $order->isPaid()) {
            $order->forceFill([
                'payment_status' => 'expired',
            ])->save();
        }
    }

    /**
     * Total yang ditagihkan ke Midtrans (integer Rupiah tanpa desimal).
     */
    public function grossAmount(Order $order): int
    {
        $source = (float) ($order->final_total ?: $order->total_harga);

        return (int) round($source);
    }

    /**
     * Format order_id yang dikirim ke Midtrans (hanya boleh berisi
     * dash, underscore, tilde, atau dot).
     */
    public function buildMidtransOrderId(Order $order): string
    {
        return 'ORDER-'.$order->id_order.'-'.now()->format('YmdHis');
    }

    /**
     * Cari URL gambar QR pada array actions respons charge.
     */
    public function extractQrCodeUrl(array $response): ?string
    {
        $actions = $response['actions'] ?? [];

        if (! is_array($actions)) {
            return null;
        }

        $fallback = null;

        foreach ($actions as $action) {
            $name = $action['name'] ?? null;
            $url = $action['url'] ?? null;

            if (empty($url)) {
                continue;
            }

            if ($name === 'generate-qr-code') {
                return $url;
            }

            if ($name === 'generate-qr-code-v2') {
                $fallback = $url;
            }
        }

        return $fallback;
    }

    /**
     * Bangun payload charge QRIS sesuai dokumentasi Midtrans Core API.
     *
     * Jumlah item_details wajib persis sama dengan gross_amount; selisih
     * diskon/pembulatan diratakan ke satu item "Diskon & Penyesuaian".
     */
    protected function buildChargePayload(Order $order, string $midtransOrderId, int $grossAmount): array
    {
        $user = $order->user;
        $customerName = $order->customer_name ?: $order->nama_pelanggan ?: 'Pelanggan';

        return [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $this->buildItemDetails($order, $grossAmount),
            'customer_details' => [
                'first_name' => $this->truncate((string) $customerName, 255),
                'email' => $user?->email ?? 'customer@bercocafe.test',
                'phone' => $order->customer_phone ?: ($user?->phone ?? '080000000000'),
            ],
            'qris' => [
                'acquirer' => (string) config('services.midtrans.qris_acquirer', 'gopay'),
            ],
        ];
    }

    /**
     * Susun rincian item agar totalnya = gross_amount.
     */
    protected function buildItemDetails(Order $order, int $grossAmount): array
    {
        $items = [];
        $sum = 0;

        foreach ($order->orderItems as $item) {
            $quantity = max(1, (int) $item->quantity);
            $price = (int) round(((float) $item->subtotal) / $quantity);

            $items[] = [
                'id' => 'ITEM-'.$item->getKey(),
                'price' => $price,
                'quantity' => $quantity,
                'name' => $this->truncate((string) ($item->menu?->nama_menu ?? 'Menu'), 50),
            ];

            $sum += $price * $quantity;
        }

        $serviceCharge = (int) round((float) $order->service_charge);

        if ($serviceCharge > 0) {
            $items[] = [
                'id' => 'SVC-'.$order->getKey(),
                'price' => $serviceCharge,
                'quantity' => 1,
                'name' => 'Biaya Take-Away',
            ];

            $sum += $serviceCharge;
        }

        $balance = $grossAmount - $sum;

        if ($balance !== 0) {
            $items[] = [
                'id' => ($balance < 0 ? 'DISC-' : 'FEE-').$order->getKey(),
                'price' => $balance,
                'quantity' => 1,
                'name' => $balance < 0 ? 'Diskon & Penyesuaian' : 'Biaya Lainnya',
            ];
        }

        if ($items === []) {
            $items[] = [
                'id' => 'ITEM-'.$order->getKey(),
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => 'Pesanan Berco Cafe',
            ];
        }

        return $items;
    }

    /**
     * Ambil expiry dari respons Midtrans atau default 15 menit dari sekarang.
     */
    protected function resolveExpiry(array $response): Carbon
    {
        return $this->parseMidtransTime($response['expiry_time'] ?? null)
            ?? now()->addMinutes(self::EXPIRY_MINUTES);
    }

    /**
     * Waktu Midtrans dikirim dalam format WIB (GMT+7); aplikasi juga berjalan
     * di Asia/Jakarta sehingga cukup diparse apa adanya.
     */
    protected function parseMidtransTime(mixed $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    protected function truncate(string $value, int $length): string
    {
        return mb_substr($value, 0, $length);
    }

    /**
     * Syarat lunas versi Midtrans: status_code 200, fraud accept,
     * transaction_status settlement atau capture.
     */
    protected function isPaidNotification(array $payload): bool
    {
        return (string) ($payload['status_code'] ?? '') === '200'
            && ($payload['fraud_status'] ?? null) === 'accept'
            && in_array($payload['transaction_status'] ?? null, ['settlement', 'capture'], true);
    }
}
