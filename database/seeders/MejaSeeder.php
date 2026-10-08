<?php

namespace Database\Seeders;

use App\Exceptions\TableQrCodeException;
use App\Models\Meja;
use App\Models\TableQrCode;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MejaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = ['Indoor', 'Indoor', 'Indoor', 'Outdoor', 'Outdoor', 'VIP'];

        for ($i = 1; $i <= 12; $i++) {
            $nama = str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            $meja = Meja::firstOrCreate(
                ['nama_meja' => 'Meja '.$nama],
                [
                    'kapasitas' => $i <= 4 ? 2 : 4,
                    'status' => 'tersedia',
                    'area' => $areas[($i - 1) % count($areas)],
                    'is_active' => true,
                ]
            );

            $this->ensureSampleToken($meja);
        }
    }

    /**
     * Sediakan token QR contoh per meja supaya halaman /order?token=...
     * bisa dicoba langsung tanpa generate manual dari dashboard admin.
     *
     * Bila ImageMagick tidak tersedia di mesin, tetap buat record token
     * tanpa file PNG (admin tinggal regenerate untuk membuat gambar).
     */
    private function ensureSampleToken(Meja $meja): void
    {
        $hasActiveToken = TableQrCode::forTable($meja->id_meja)
            ->active()
            ->valid()
            ->exists();

        if ($hasActiveToken) {
            return;
        }

        try {
            // Tanpa expiry supaya token contoh tidak kedaluwarsa mendadak.
            app(QrCodeService::class)->generateForTable($meja->id_meja, 0);

            return;
        } catch (TableQrCodeException|\Throwable $e) {
            if (TableQrCode::forTable($meja->id_meja)->active()->exists()) {
                return;
            }
        }

        TableQrCode::create([
            'table_id' => $meja->id_meja,
            'token' => (string) Str::uuid(),
            'version' => 1,
            'expired_at' => null,
            'is_active' => true,
            'generated_by' => null,
            'image_path' => null,
        ]);
    }
}
