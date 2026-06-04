<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrisTransaction extends Model
{
    protected $table = 'qris_transactions';
    protected $primaryKey = 'id_qris_transaction';
    public $timestamps = true;

    protected $fillable = [
        'id_order',
        'qris_code',
        'transaction_id',
        'invoice_id',
        'amount',
        'status',
        'payment_channel',
        'customer_name',
        'customer_email',
        'customer_phone',
        'expires_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function reconciliation()
    {
        return $this->hasOne(QrisReconciliation::class, 'id_qris_transaction', 'id_qris_transaction');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeQrisOnly($query)
    {
        return $query->where('payment_channel', 'qris');
    }

    /**
     * Check if transaction is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at) && $this->status === 'pending';
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($transactionId = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id,
        ]);

        // Update order payment status
        if ($this->order) {
            $this->order->update(['status_pembayaran' => 'Paid']);
        }

        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason]),
        ]);

        // Update order payment status
        if ($this->order) {
            $this->order->update(['status_pembayaran' => 'Failed']);
        }

        return $this;
    }
}
