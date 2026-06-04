<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Exception;

class TestXenditPayment extends Command
{
    protected $signature = 'xendit:test {--amount=100000 : Amount in IDR} {--description="Test Payment" : Invoice description}';
    protected $description = 'Test Xendit payment integration by creating a test invoice';

    public function handle()
    {
        try {
            Configuration::setXenditKey(config('services.xendit.secret_key'));

            $amount = $this->option('amount');
            $description = $this->option('description');

            $this->info('Creating test Xendit invoice...');
            $this->line('Amount: Rp ' . number_format($amount, 0, ',', '.'));
            $this->line('Description: ' . $description);
            $this->newLine();

            $apiInstance = new InvoiceApi();
            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => 'TEST-' . time() . '-' . rand(1000, 9999),
                'amount' => (int) $amount,
                'description' => $description,
                'currency' => 'IDR',
                'reminder_time' => 1,
                'payment_methods' => [
                    'BANK_TRANSFER',
                    'DEBIT_CARD',
                    'CREDIT_CARD',
                    'OVO',
                    'DANA',
                    'LINKAJA',
                ]
            ]);

            $invoice = $apiInstance->createInvoice($createInvoiceRequest);

            $this->info('✓ Invoice created successfully!');
            $this->newLine();
            $this->table(
                ['Key', 'Value'],
                [
                    ['Invoice ID', $invoice->getId()],
                    ['External ID', $invoice->getExternalId()],
                    ['Status', $invoice->getStatus()],
                    ['Amount', 'Rp ' . number_format($invoice->getAmount(), 0, ',', '.')],
                    ['Invoice URL', $invoice->getInvoiceUrl() ?? 'N/A'],
                ]
            );
            $this->newLine();

            $this->info('📱 Payment Page URL:');
            $this->line($invoice->getInvoiceUrl());
            $this->newLine();

            $this->warn('⚠️  For testing in DEVELOPMENT mode:');
            $this->line('1. Open the link above in your browser');
            $this->line('2. Use test card or e-wallet credentials');
            $this->line('3. Complete the payment');
            $this->line('4. Check webhook/callback status');

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
