<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Voucher;
use App\Notifications\ComebackVoucherNotification;
use App\Notifications\InactiveUserReminderNotification;
use Illuminate\Console\Command;

class SendComebackVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:send-comeback {--days=30 : Jumlah hari inaktif}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Kirim voucher promo comeback ke pelanggan yang tidak aktif lebih dari 30 hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        
        $this->info("Mencari pengguna yang tidak aktif selama {$days} hari atau lebih...");

        // Get inactive users
        $inactiveUsers = User::where(function ($query) use ($days) {
            $query->whereNull('last_activity_at')
                ->orWhere('last_activity_at', '<=', now()->subDays($days));
        })
        ->where('email_verified_at', '!=', null) // Only verified users
        ->get();

        $this->info("Ditemukan " . $inactiveUsers->count() . " pengguna yang tidak aktif.");

        if ($inactiveUsers->isEmpty()) {
            $this->info('Tidak ada pengguna yang tidak aktif.');
            return Command::SUCCESS;
        }

        // Get comeback voucher
        $comebackVoucher = Voucher::where('type', 'comeback')
            ->where('is_active', true)
            ->where('valid_until', '>=', now())
            ->first();

        if (!$comebackVoucher) {
            $this->warn('Tidak ada voucher comeback yang aktif. Silakan buat voucher terlebih dahulu.');
            return Command::FAILURE;
        }

        $this->info("Menggunakan voucher: " . $comebackVoucher->name);

        // Send notifications and vouchers
        $sentCount = 0;
        $failedCount = 0;

        foreach ($inactiveUsers as $user) {
            try {
                // Check if user already has this voucher
                $hasVoucher = $user->vouchers()
                    ->where('voucher_id', $comebackVoucher->id)
                    ->exists();

                if (!$hasVoucher) {
                    // Attach voucher to user
                    $user->vouchers()->attach($comebackVoucher->id, [
                        'status' => 'active',
                        'notified_at' => now(),
                    ]);

                    // Send notifications
                    $user->notify(new ComebackVoucherNotification($comebackVoucher));
                    $user->notify(new InactiveUserReminderNotification());

                    $sentCount++;
                    $this->line("✓ Voucher dikirim ke: {$user->email}");
                } else {
                    $this->line("⊘ {$user->email} sudah memiliki voucher ini");
                }
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("✗ Gagal mengirim ke {$user->email}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("=========== RINGKASAN ===========");
        $this->info("Total pengguna tidak aktif: " . $inactiveUsers->count());
        $this->info("Voucher terkirim: " . $sentCount);
        $this->warn("Gagal: " . $failedCount);
        $this->info("================================");

        return Command::SUCCESS;
    }
}
