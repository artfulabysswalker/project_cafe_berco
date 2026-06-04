<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrisReconciliation extends Model
{
    protected $table = 'qris_reconciliations';
    protected $primaryKey = 'id_reconciliation';
    public $timestamps = true;

    protected $fillable = [
        'id_qris_transaction',
        'reference_id',
        'reconciliation_status',
        'bank_amount',
        'system_amount',
        'amount_difference',
        'notes',
        'bank_transaction_date',
        'reconciled_at',
        'reconciled_by',
    ];

    protected $casts = [
        'bank_amount' => 'decimal:2',
        'system_amount' => 'decimal:2',
        'amount_difference' => 'decimal:2',
        'bank_transaction_date' => 'datetime',
        'reconciled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function qrisTransaction()
    {
        return $this->belongsTo(QrisTransaction::class, 'id_qris_transaction', 'id_qris_transaction');
    }

    public function reconciledBy()
    {
        return $this->belongsTo(User::class, 'reconciled_by', 'id_user');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('reconciliation_status', 'pending');
    }

    public function scopeMatched($query)
    {
        return $query->where('reconciliation_status', 'matched');
    }

    public function scopeMismatched($query)
    {
        return $query->where('reconciliation_status', 'mismatched');
    }

    public function scopeResolved($query)
    {
        return $query->where('reconciliation_status', 'resolved');
    }

    /**
     * Check if amounts match
     */
    public function amountsMatch(): bool
    {
        return abs($this->bank_amount - $this->system_amount) < 0.01;
    }

    /**
     * Mark as matched
     */
    public function markAsMatched($reconciledBy = null)
    {
        $this->update([
            'reconciliation_status' => 'matched',
            'amount_difference' => 0,
            'reconciled_at' => now(),
            'reconciled_by' => $reconciledBy,
        ]);

        return $this;
    }

    /**
     * Mark as mismatched
     */
    public function markAsMismatched($difference, $notes = null)
    {
        $this->update([
            'reconciliation_status' => 'mismatched',
            'amount_difference' => $difference,
            'notes' => $notes ?? 'Amount mismatch detected during reconciliation',
        ]);

        return $this;
    }

    /**
     * Mark as resolved
     */
    public function markAsResolved($reconciledBy = null, $notes = null)
    {
        $this->update([
            'reconciliation_status' => 'resolved',
            'reconciled_at' => now(),
            'reconciled_by' => $reconciledBy,
            'notes' => $notes,
        ]);

        return $this;
    }
}
