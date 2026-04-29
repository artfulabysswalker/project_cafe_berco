<?php

namespace App\Console\Commands;

use App\Services\VoucherService;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendInactiveCustomerVouchersCommand extends Command
{
    protected $signature = 'inactive-customers:send-vouchers';
    protected $description = 'Send automatic vouchers to inactive customers (not visited for 30+ days)';

    public function __construct(
        private VoucherService $voucherService,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting to send vouchers to inactive customers...');

        try {
            $voucherCount = $this->voucherService->sendVouchersToInactiveCustomers();
            $this->info("Sent vouchers to {$voucherCount} customer(s)");

            $reminderCount = $this->notificationService->sendComebackReminders();
            $this->info("Sent comeback reminders to {$reminderCount} customer(s)");

            $this->info('✓ Process completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
