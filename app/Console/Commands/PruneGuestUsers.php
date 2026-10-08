<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Membersihkan user guest yang sudah tidak relevan.
 *
 * Karena setiap sesi browser kini membuat user guest sendiri, tabel users
 * bertambah satu baris per kunjungan. Perintah ini menghapus guest yang
 * sudah lama tidak aktif DAN tidak punya order, sehingga tidak ada data
 * transaksi yang ikut terhapus (orders.id_user adalah FK NOT NULL).
 */
class PruneGuestUsers extends Command
{
    protected $signature = 'guests:prune
                            {--days=14 : Umur minimal guest (hari) sebelum boleh dihapus}
                            {--dry-run : Tampilkan yang akan dihapus tanpa mengubah data}';

    protected $description = 'Hapus user guest yang sudah lama tidak aktif dan tidak punya pesanan';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');

        $query = User::where('is_guest', true)
            ->where('updated_at', '<', now()->subDays($days))
            ->whereDoesntHave('orders')
            ->whereDoesntHave('cartItems');

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info("Tidak ada user guest yang perlu dibersihkan (umur > {$days} hari).");

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->line("Akan menghapus {$count} user guest:");

            foreach ((clone $query)->limit(20)->get(['id_user', 'username', 'updated_at']) as $guest) {
                $this->line(sprintf('  #%d %s (terakhir aktif %s)', $guest->id_user, $guest->username, $guest->updated_at?->format('Y-m-d H:i')));
            }

            if ($count > 20) {
                $this->line('  ... dan '.($count - 20).' lainnya.');
            }

            return self::SUCCESS;
        }

        // Potong per batch supaya tidak menumpuk query DELETE besar.
        $deleted = 0;

        do {
            $batch = (clone $query)->limit(200)->get();

            if ($batch->isEmpty()) {
                break;
            }

            $deleted += $batch->each->delete()->count();
        } while ($batch->count() === 200);

        $this->info("Selesai. {$deleted} user guest dihapus.");

        return self::SUCCESS;
    }
}
