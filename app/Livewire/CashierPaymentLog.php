<?php

namespace App\Livewire;

use App\Models\PaymentLog;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Log transfer QRIS di dashboard kasir: setiap pembayaran masuk tercatat
 * otomatis dari notifikasi Midtrans, tanpa cek bukti transfer manual.
 */
class CashierPaymentLog extends Component
{
    use WithPagination;

    public const STATUSES = [
        'all' => 'Semua Status',
        'pending' => 'Menunggu',
        'settlement' => 'Lunas',
        'capture' => 'Lunas (Capture)',
        'expire' => 'Kedaluwarsa',
        'deny' => 'Ditolak',
        'cancel' => 'Dibatalkan',
    ];

    public string $status = 'all';

    public ?string $date = null;

    /**
     * Id log terakhir yang sudah pernah terlihat kasir.
     */
    public int $highlightAfterId = 0;

    /**
     * Id log yang baru masuk sejak poll terakhir (diberi animasi sorot).
     *
     * @var array<int, int>
     */
    public array $highlightedIds = [];

    public function mount(): void
    {
        $this->highlightAfterId = (int) (PaymentLog::max('id') ?? 0);
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingDate(): void
    {
        $this->resetPage();
    }

    /**
     * Polling berkala (cadangan utama saat websocket tidak aktif).
     */
    public function poll(): void
    {
        $this->syncHighlights();
    }

    /**
     * Dipicu dari listener Echo di view saat ada broadcast PaymentReceived,
     * supaya baris baru muncul seketika tanpa menunggu poll berikutnya.
     */
    #[On('qris-payment-received')]
    public function refresh(): void
    {
        $this->syncHighlights();
    }

    public function render()
    {
        $logs = PaymentLog::with('order')
            ->when($this->status !== 'all', function ($query) {
                $query->where('transaction_status', $this->status);
            })
            ->when($this->date, function ($query) {
                $query->whereDate('created_at', $this->date);
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.cashier-payment-log', [
            'logs' => $logs,
            'statuses' => self::STATUSES,
            'paidToday' => PaymentLog::whereDate('paid_at', today())->sum('gross_amount'),
        ]);
    }

    /**
     * Cari log yang masuk sejak pemeriksaan terakhir lalu tandai untuk disorot.
     */
    protected function syncHighlights(): void
    {
        $maxId = (int) (PaymentLog::max('id') ?? 0);

        if ($maxId <= $this->highlightAfterId) {
            $this->highlightedIds = [];

            return;
        }

        $this->highlightedIds = PaymentLog::where('id', '>', $this->highlightAfterId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->highlightAfterId = $maxId;
    }
}
