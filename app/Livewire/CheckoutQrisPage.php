<?php

namespace App\Livewire;

use App\Exceptions\QrisChargeException;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Services\QrisPaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Halaman pembayaran QRIS pelanggan: menampilkan QR, total tagihan,
 * countdown, dan status pembayaran yang diperbarui otomatis.
 */
class CheckoutQrisPage extends Component
{
    public Order $order;

    public ?PaymentLog $paymentLog = null;

    public ?string $errorMessage = null;

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->authorizeOrder();

        if ($this->order->isPaid()) {
            $this->redirect(route('order.show', $this->order->id_order));

            return;
        }

        if (strtolower((string) $this->order->payment_method) !== 'qris') {
            $this->redirect(route('order.waiting', $this->order->id_order));

            return;
        }

        $this->loadPaymentLog();
        $this->ensureCharge();
    }

    /**
     * Perbarui status dari database (dipanggil wire:poll) tanpa memanggil
     * API Midtrans agar tidak membebani rate limit.
     */
    public function pollStatus(): void
    {
        $this->order->refresh();
        $this->loadPaymentLog();

        if ($this->paymentLog?->isPaid() || $this->order->isPaid()) {
            $this->redirect(route('order.show', $this->order->id_order));
        }
    }

    /**
     * Cek status langsung ke Midtrans (tombol manual), berguna saat webhook
     * belum terkirim pada environment lokal.
     */
    public function refreshStatus(): void
    {
        $this->order->refresh();
        $this->loadPaymentLog();

        $log = $this->paymentLog;

        if (! $log || $log->isPaid()) {
            $this->pollStatus();

            return;
        }

        if ($log->isExpired()) {
            $this->paymentLog = app(QrisPaymentService::class)->applyStatus($log, [
                'status_code' => '200',
                'transaction_status' => 'expire',
            ]);
            $this->loadPaymentLog();

            return;
        }

        $service = app(QrisPaymentService::class);
        $payload = $service->fetchStatus($log);

        if ($payload !== null) {
            $this->paymentLog = $service->applyStatus($log, $payload);
        }

        $this->order->refresh();

        if ($this->paymentLog?->isPaid() || $this->order->isPaid()) {
            $this->redirect(route('order.show', $this->order->id_order));
        }
    }

    /**
     * Buat QR baru setelah kode sebelumnya kedaluwarsa.
     */
    public function regenerateQr(): void
    {
        if ($this->order->isPaid()) {
            $this->redirect(route('order.show', $this->order->id_order));

            return;
        }

        $this->errorMessage = null;
        $this->ensureCharge(force: true);
    }

    #[Computed]
    public function total(): int
    {
        return app(QrisPaymentService::class)->grossAmount($this->order);
    }

    #[Computed]
    public function isPaid(): bool
    {
        return $this->paymentLog?->isPaid() || $this->order->isPaid();
    }

    #[Computed]
    public function isExpired(): bool
    {
        return ! $this->isPaid && $this->paymentLog !== null && $this->paymentLog->isExpired();
    }

    public function render()
    {
        return view('livewire.checkout-qris-page', [
            'order' => $this->order,
            'paymentLog' => $this->paymentLog,
            'expiresInSeconds' => $this->paymentLog?->secondsRemaining() ?? 0,
        ]);
    }

    /**
     * Pesanan hanya boleh dilihat pemiliknya (admin/staf dibebaskan).
     */
    private function authorizeOrder(): void
    {
        $user = Auth::user();

        $allowed = $user && ($user->isAdmin() || $user->isStaff()
            || (int) $this->order->id_user === (int) $user->id_user);

        abort_unless($allowed, 403);
    }

    private function loadPaymentLog(): void
    {
        $this->paymentLog = $this->order->latestPaymentLog;
    }

    /**
     * Ambil charge QRIS terbaru; buat baru bila belum ada atau sudah
     * kedaluwarsa. Kegagalan API ditampilkan sebagai pesan inline.
     */
    private function ensureCharge(bool $force = false): void
    {
        $existing = $this->order->latestPaymentLog;

        if (! $force && $existing && $existing->qr_code_url && ! $existing->isExpired()) {
            $this->paymentLog = $existing;

            return;
        }

        try {
            $this->paymentLog = app(QrisPaymentService::class)->createQrisCharge($this->order);
        } catch (QrisChargeException $e) {
            report($e);
            $this->errorMessage = $e->getMessage();
        }
    }
}
