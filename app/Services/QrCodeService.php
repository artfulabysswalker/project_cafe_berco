<?php

namespace App\Services;

use App\Exceptions\TableQrCodeException;
use App\Models\Meja;
use App\Models\TableQrCode;
use App\Services\QRCode\QrCodeRenderer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class QrCodeService
{
    public function __construct(protected QrCodeRenderer $renderer)
    {
        //
    }

    /**
     * @param  int|string  $tableId
     */
    public function generateForTable($tableId, ?int $expiresInHours = null): TableQrCode
    {
        $expiresInHours = $expiresInHours ?? (int) config('qrcode.default_expires_in_hours', 24);
        $table = Meja::find($tableId);
        if (! $table) {
            throw new TableQrCodeException('Meja tidak ditemukan');
        }

        $token = (string) Str::uuid();
        $now = now();
        $expiredAt = $expiresInHours > 0 ? $now->copy()->addHours($expiresInHours) : null;

        return DB::transaction(function () use ($table, $token, $expiredAt) {
            // Lock baris meja agar version tidak bentrok saat dua admin
            // clicking generate bersamaan untuk meja yang sama.
            Meja::whereKey($table->id_meja)->lockForUpdate()->first();

            $version = $this->nextVersion($table->id_meja);
            $issuedAt = now();

            if ((bool) config('qrcode.encode_as_signed_url', true)) {
                $qrData = $this->withBaseUrl(fn () => URL::temporarySignedRoute(
                    'customer.qrcode.scan',
                    $expiredAt ?? $issuedAt->copy()->addYears(10),
                    ['token' => $token]
                ));
            } else {
                $qrData = Crypt::encryptString(json_encode([
                    'table_id' => $table->id_meja,
                    'table_name' => $table->nama_meja,
                    'token' => $token,
                    'version' => $version,
                    'issued_at' => $issuedAt->toIso8601String(),
                    'expired_at' => $expiredAt?->toIso8601String(),
                ], JSON_UNESCAPED_UNICODE));
            }

            $prefix = rtrim(config('qrcode.path_prefix', 'qrcodes'), '/');
            $relativePath = sprintf('%s/%d/%s.png', $prefix, $table->id_meja, $token);

            try {
                $this->renderer->generatePng($qrData, $relativePath, [
                    'size' => (int) config('qrcode.size', 256),
                    'margin' => (int) config('qrcode.margin', 4),
                ]);
            } catch (\Throwable $e) {
                Log::error('Gagal merender QR Code', [
                    'table_id' => $table->id_meja,
                    'error' => $e->getMessage(),
                ]);

                throw new TableQrCodeException('Gagal menyimpan file QR Code: '.$e->getMessage(), 0, $e);
            }

            try {
                return TableQrCode::create([
                    'table_id' => $table->id_meja,
                    'token' => $token,
                    'version' => $version,
                    'expired_at' => $expiredAt,
                    'is_active' => true,
                    'generated_by' => Auth::id(),
                    'image_path' => $relativePath,
                ]);
            } catch (\Throwable $e) {
                // Jangan tinggalkan file PNG yatim bila insert DB gagal
                $this->deleteImage($relativePath);

                throw new TableQrCodeException('Gagal menyimpan data QR Code: '.$e->getMessage(), 0, $e);
            }
        });
    }

    /**
     * Version berikutnya untuk sebuah meja (selalu naik, tidak pernah reuse).
     * Panggil dari dalam transaksi yang sudah lock baris meja.
     */
    public function nextVersion(int $tableId): int
    {
        return (int) TableQrCode::forTable($tableId)->max('version') + 1;
    }

    public function validateToken(string $token): TableQrCode
    {
        $qr = TableQrCode::where('token', $token)->first();
        if (! $qr) {
            throw new TableQrCodeException('QR Code tidak valid');
        }
        if (! $qr->is_active) {
            throw new TableQrCodeException('QR Code telah dinonaktifkan');
        }
        if ($qr->isExpired()) {
            throw new TableQrCodeException('QR Code telah kedaluwarsa');
        }

        return $qr;
    }

    /**
     * Validasi token untuk halaman order: token harus hidup DAN meja yang
     * ditunjuk harus aktif. Meja yang dinonaktifkan admin tidak boleh
     * menerima pesanan walaupun QR-nya masih berlaku.
     *
     * @throws TableQrCodeException
     */
    public function validateTokenForOrder(string $token): TableQrCode
    {
        $qr = $this->validateToken($token);

        if (! $qr->table) {
            throw new TableQrCodeException('QR Code tidak valid');
        }

        if (! $qr->table->isActive()) {
            throw new TableQrCodeException('Meja ini sedang tidak aktif');
        }

        return $qr;
    }

    public function revoke(string $token, bool $deleteImage = false): TableQrCode
    {
        $qr = TableQrCode::where('token', $token)->first();
        if (! $qr) {
            throw new TableQrCodeException('QR Code tidak ditemukan');
        }
        $qr->is_active = false;
        $qr->save();

        if ($deleteImage) {
            $this->deleteImage($qr->image_path);
        }

        return $qr;
    }

    public function revokeAllForTable(int|string $tableId): void
    {
        TableQrCode::forTable((int) $tableId)->active()->get()->each(function (TableQrCode $qr) {
            $qr->is_active = false;
            $qr->save();
            $this->deleteImage($qr->image_path);
        });
    }

    public function regenerate(int|string $tableId, ?int $expiresInHours = null): TableQrCode
    {
        $tableId = (int) $tableId;

        return DB::transaction(function () use ($tableId, $expiresInHours) {
            $this->revokeAllForTable($tableId);

            return $this->generateForTable($tableId, $expiresInHours);
        });
    }

    public function decryptPayload(string $encrypted): array
    {
        try {
            $json = Crypt::decryptString($encrypted);
        } catch (DecryptException) {
            throw new TableQrCodeException('Tidak dapat mendekripsi payload QR Code');
        }

        $data = json_decode($json, true);
        if (! is_array($data) || ! isset($data['token']) || ! is_string($data['token'])) {
            throw new TableQrCodeException('Payload QR Code tidak valid');
        }

        return $data;
    }

    public function imagePath(TableQrCode $qr): string
    {
        if (! empty($qr->image_path)) {
            return $qr->image_path;
        }

        $prefix = rtrim(config('qrcode.path_prefix', 'qrcodes'), '/');

        return sprintf('%s/%d/%s.png', $prefix, $qr->table_id, $qr->token);
    }

    public function imageExists(TableQrCode $qr): bool
    {
        return Storage::disk(config('qrcode.disk', 'public'))->exists($this->imagePath($qr));
    }

    /**
     * URL yang dipindai customer. Signed URL bersifat deterministik untuk
     * token + expiry yang sama, jadi selalu cocok dengan isi QR Code.
     */
    public function scanUrlFor(TableQrCode $qr): string
    {
        return $this->withBaseUrl(fn () => URL::temporarySignedRoute(
            'customer.qrcode.scan',
            $qr->expired_at ?? now()->addYears(10),
            ['token' => $qr->token]
        ));
    }

    /**
     * Jalankan callback dengan base URL QR yang benar bila sudah dikonfigurasi.
     *
     * Dipakai supaya URL yang ditanam ke PNG tidak ikut APP_URL ("localhost")
     * yang tidak bisa dijangkau HP pelanggan. Setelah callback selesai, root URL
     * dikembalikan seperti semula supaya route() lain di request yang sama
     * tidak ikut berubah.
     */
    protected function withBaseUrl(callable $callback): mixed
    {
        $baseUrl = config('qrcode.base_url');

        if (blank($baseUrl)) {
            return $callback();
        }

        $generator = URL::getFacadeRoot();
        $generator->forceRootUrl($baseUrl);

        try {
            return $callback();
        } finally {
            $generator->forceRootUrl(null);
        }
    }

    /**
     * Representasi payload sesuai mode aktif, untuk ditampilkan di halaman
     * admin. Penting: payload dienkripsi bersifat acak (nonce berbeda tiap
     * pemanggilan), jadi string ini benar-benar valid tapi tidak identik
     * piksel dengan yang tertanam di PNG. Hanya untuk keperluan verifikasi.
     */
    public function payloadFor(TableQrCode $qr): string
    {
        if (! (bool) config('qrcode.encode_as_signed_url', true)) {
            return Crypt::encryptString(json_encode([
                'table_id' => $qr->table_id,
                'table_name' => $qr->table?->nama_meja,
                'token' => $qr->token,
                'version' => $qr->version,
                'issued_at' => $qr->created_at?->toIso8601String(),
                'expired_at' => $qr->expired_at?->toIso8601String(),
            ], JSON_UNESCAPED_UNICODE));
        }

        return $this->scanUrlFor($qr);
    }

    private function deleteImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $disk = config('qrcode.disk', 'public');

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (\Throwable $e) {
            // Kegagalan hapus file tidak boleh membatalkan proses revoke
            Log::warning('Gagal menghapus file QR Code', ['path' => $path, 'error' => $e->getMessage()]);
        }
    }
}
