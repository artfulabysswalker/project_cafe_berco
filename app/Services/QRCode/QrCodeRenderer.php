<?php

namespace App\Services\QRCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class QrCodeRenderer
{
    private ?string $magick = null;

    /**
     * Render payload QR Code lalu simpan sebagai PNG di disk yang diminta.
     *
     * BaconQrCode hanya bisa menghasilkan SVG secara pure-PHP, jadi file SVG
     * SVG-nya dikonversi ke PNG memakai ImageMagick CLI (ext-gd/imagick tidak
     * diperlukan di mesin ini).
     */
    public function generatePng(string $data, string $relativePath, array $options = []): array
    {
        if (trim($data) === '') {
            throw new RuntimeException('Payload QR Code tidak boleh kosong');
        }

        $size = (int) ($options['size'] ?? config('qrcode.size', 256));
        $margin = (int) ($options['margin'] ?? config('qrcode.margin', 4));
        $disk = $options['disk'] ?? config('qrcode.disk', 'public');

        $svg = $this->renderSvg($data, $size, $margin, (string) ($options['error_correction'] ?? config('qrcode.error_correction', 'M')));

        [$svgFile, $pngFile] = $this->makeTempFiles();
        $cleanup = function () use ($svgFile, $pngFile) {
            @unlink($svgFile);
            @unlink($pngFile);
        };

        try {
            if (file_put_contents($svgFile, $svg) === false) {
                throw new RuntimeException('Gagal menulis file SVG sementara');
            }

            $png = $this->convertSvgToPng($svgFile, $pngFile);
        } catch (\Throwable $e) {
            $cleanup();

            throw $e instanceof RuntimeException
                ? $e
                : new RuntimeException('Gagal membuat QR Code: '.$e->getMessage(), 0, $e);
        }

        $cleanup();

        if (Storage::disk($disk)->put($relativePath, $png) !== true) {
            throw new RuntimeException('Gagal menyimpan QR Code ke disk '.$disk);
        }

        return [
            'disk' => $disk,
            'path' => $relativePath,
            'full_path' => Storage::disk($disk)->path($relativePath),
            'url' => Storage::disk($disk)->url($relativePath),
            'bytes' => strlen($png),
        ];
    }

    private function renderSvg(string $data, int $size, int $margin, string $ecLevel): string
    {
        try {
            $writer = new Writer(
                new ImageRenderer(
                    new RendererStyle(max(1, $size), max(0, $margin)),
                    new SvgImageBackEnd
                )
            );

            return $writer->writeString(
                $data,
                Encoder::DEFAULT_BYTE_MODE_ENCODING,
                $this->errorCorrectionLevel($ecLevel)
            );
        } catch (\Throwable $e) {
            throw new RuntimeException('Gagal merender SVG QR Code: '.$e->getMessage(), 0, $e);
        }
    }

    private function errorCorrectionLevel(string $level): ErrorCorrectionLevel
    {
        return match (strtoupper($level)) {
            'L' => ErrorCorrectionLevel::L(),
            'Q' => ErrorCorrectionLevel::Q(),
            'H' => ErrorCorrectionLevel::H(),
            default => ErrorCorrectionLevel::M(),
        };
    }

    private function convertSvgToPng(string $svgFile, string $pngFile): string
    {
        $magick = $this->resolveMagick();
        $command = escapeshellarg($magick);
        if (basename($magick) === 'magick') {
            $command .= ' convert';
        }

        $cmd = $command
            .' -density 300 '.escapeshellarg($svgFile)
            .' -background none '.escapeshellarg($pngFile)
            .' 2>&1';

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0 || ! is_file($pngFile) || filesize($pngFile) === 0) {
            Log::error('ImageMagick gagal mengonversi QR SVG ke PNG', [
                'binary' => $magick,
                'exit_code' => $returnVar,
                'output' => implode(' ', $output),
            ]);

            throw new RuntimeException('Konversi QR ke PNG gagal: '.trim(implode(' ', $output)));
        }

        $png = file_get_contents($pngFile);
        if ($png === false) {
            throw new RuntimeException('Gagal membaca hasil konversi PNG');
        }

        // Validasi magic byte PNG supaya file rusak tidak pernah tersimpan
        if (! str_starts_with($png, "\x89PNG\r\n\x1a\n")) {
            throw new RuntimeException('Hasil konversi bukan file PNG yang valid');
        }

        return $png;
    }

    private function resolveMagick(): string
    {
        if ($this->magick !== null) {
            return $this->magick;
        }

        $candidates = array_unique(array_filter(array_merge(
            [config('qrcode.magick_path')],
            (array) config('qrcode.magick_fallbacks', [])
        )));

        foreach ($candidates as $candidate) {
            if ($this->isUsable($candidate)) {
                return $this->magick = $candidate;
            }
        }

        throw new RuntimeException(
            'ImageMagick tidak ditemukan. Pasang ImageMagick atau atur MAGICK_PATH di .env'
        );
    }

    private function isUsable(string $binary): bool
    {
        if (str_contains($binary, '/')) {
            return is_file($binary) && is_executable($binary);
        }

        exec('command -v '.escapeshellarg($binary).' 2>/dev/null', $out, $code);

        return $code === 0 && ! empty($out);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function makeTempFiles(): array
    {
        $dir = rtrim(sys_get_temp_dir(), '/').'/cafe-berco-qr';

        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('Gagal membuat direktori temporary QR Code');
        }

        $name = bin2hex(random_bytes(8));

        return [$dir.'/'.$name.'.svg', $dir.'/'.$name.'.png'];
    }
}
