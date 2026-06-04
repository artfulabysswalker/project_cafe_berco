<?php

namespace App\Console\Commands;

use App\Models\QrisTransaction;
use App\Models\QrisReconciliation;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ReconcileQrisPayments extends Command
{
    protected $signature = 'qris:reconcile 
                          {--date= : Specific date to reconcile (Y-m-d)}
                          {--days=1 : Number of days to reconcile}
                          {--force : Skip confirmation}';

    protected $description = 'Reconcile QRIS payments with bank records';

    public function handle()
    {
        $this->info('🔄 Starting QRIS Reconciliation...');

        $date = $this->option('date') 
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : now()->subDays($this->option('days'));

        $daysToReconcile = $this->option('days');
        $endDate = $this->option('date') ? $date : now();

        if (!$this->option('force')) {
            $this->warn("📅 Will reconcile transactions from {$date->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
            if (!$this->confirm('Continue?')) {
                $this->info('Cancelled.');
                return 1;
            }
        }

        // Get unreconciled transactions
        $unreconciledTransactions = QrisTransaction::where('status', 'paid')
            ->whereBetween('paid_at', [$date->startOfDay(), $endDate->endOfDay()])
            ->get();

        if ($unreconciledTransactions->isEmpty()) {
            $this->info('✅ No transactions to reconcile.');
            return 0;
        }

        $this->info("📊 Found {$unreconciledTransactions->count()} paid transactions");

        $matched = 0;
        $mismatched = 0;
        $failed = 0;

        foreach ($unreconciledTransactions as $transaction) {
            try {
                $reconciliation = $transaction->reconciliation;

                if (!$reconciliation) {
                    $this->warn("⚠️ No reconciliation record for transaction #{$transaction->id_qris_transaction}");
                    $failed++;
                    continue;
                }

                // Check if already reconciled
                if ($reconciliation->reconciliation_status !== 'pending') {
                    $this->line("⏭️ Transaction #{$transaction->id_qris_transaction} already reconciled");
                    continue;
                }

                // In real scenario, this would fetch from bank API
                // For now, we'll simulate matching
                if ($reconciliation->amountsMatch()) {
                    $reconciliation->markAsMatched(auth()->id());
                    $this->line("✅ Transaction #{$transaction->id_qris_transaction} - MATCHED (Rp " . number_format($transaction->amount, 0) . ")");
                    $matched++;
                } else {
                    $difference = abs($reconciliation->bank_amount - $transaction->amount);
                    $reconciliation->markAsMismatched($difference);
                    $this->warn("⚠️ Transaction #{$transaction->id_qris_transaction} - MISMATCH (Difference: Rp " . number_format($difference, 0) . ")");
                    $mismatched++;
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing transaction #{$transaction->id_qris_transaction}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\n📈 Reconciliation Summary:");
        $this->info("✅ Matched: $matched");
        $this->warn("⚠️ Mismatched: $mismatched");
        $this->error("❌ Failed: $failed");

        return 0;
    }
}
