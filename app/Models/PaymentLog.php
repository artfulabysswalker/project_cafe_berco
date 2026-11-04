<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'transaction_id',
        'qr_code_url',
        'gross_amount',
        'transaction_status',
        'fraud_status',
        'payment_type',
        'raw_response',
        'signature_key',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'gross_amount' => 'integer',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id_order');
    }

    /**
     * Status lunas menurut konvensi Midtrans.
     */
    public function isPaid(): bool
    {
        return $this->paid_at !== null
            || in_array($this->transaction_status, ['settlement', 'capture'], true);
    }

    public function isExpired(): bool
    {
        return $this->transaction_status === 'expire'
            || ($this->expired_at !== null && $this->expired_at->isPast());
    }

    /**
     * Sisa detik sebelum QR kedaluwarsa (dipakai countdown halaman pelanggan).
     */
    public function secondsRemaining(): int
    {
        if ($this->isPaid()) {
            return 0;
        }

        $expiresAt = $this->expired_at ?? $this->created_at?->copy()->addMinutes(15);

        if ($expiresAt === null) {
            return 0;
        }

        return max(0, $expiresAt->getTimestamp() - time());
    }

    public function statusLabel(): string
    {
        return match ($this->transaction_status) {
            'pending' => 'Menunggu Pembayaran',
            'settlement' => 'Lunas (Settlement)',
            'capture' => 'Lunas (Capture)',
            'deny' => 'Ditolak',
            'cancel' => 'Dibatalkan',
            'expire' => 'Kedaluwarsa',
            'refund', 'partial_refund' => 'Refund',
            default => ucfirst((string) $this->transaction_status),
        };
    }

    /**
     * Kelas lencana status untuk tampilan kasir.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->transaction_status) {
            'settlement', 'capture' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'deny', 'cancel' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-stone-100 text-stone-600 border-stone-200',
        };
    }
}
