<?php

namespace App\Console\Commands;

use App\Models\QrisTransaction;
use Illuminate\Console\Command;

class DebugQrisCode extends Command
{
    protected $signature = 'qris:debug {order_id}';

    protected $description = 'Debug QRIS transaction and check stored QRIS code';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        $transaction = QrisTransaction::where('id_order', $orderId)->first();
        
        if (!$transaction) {
            $this->error("No QRIS transaction found for order #{$orderId}");
            return 1;
        }

        $this->info("=== QRIS Transaction Debug ===\n");
        
        $this->table(['Property', 'Value'], [
            ['ID', $transaction->id_qris_transaction],
            ['Order ID', $transaction->id_order],
            ['Invoice ID', $transaction->invoice_id],
            ['QRIS Code Length', strlen($transaction->qris_code ?? '')],
            ['QRIS Code (first 50 chars)', substr($transaction->qris_code ?? '', 0, 50)],
            ['QRIS Code (full)', $transaction->qris_code ?? 'NULL'],
            ['Status', $transaction->status],
            ['Amount', $transaction->amount],
            ['Created', $transaction->created_at],
        ]);
        
        $this->line('');
        
        if (!$transaction->qris_code) {
            $this->warn('⚠️ QRIS Code is EMPTY! Checking metadata...');
            $metadata = $transaction->metadata;
            if ($metadata) {
                $this->line('Metadata keys: ' . implode(', ', array_keys($metadata)));
                if (isset($metadata['qris_string'])) {
                    $this->info('Found in metadata["qris_string"]: ' . substr($metadata['qris_string'], 0, 50));
                }
            }
        } else {
            $this->info('✅ QRIS Code found and stored correctly!');
        }
        
        return 0;
    }
}
