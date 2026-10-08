<?php

return [
    'default_expires_in_hours' => env('QR_CODE_DEFAULT_EXPIRES_HOURS', 24),
    'size' => env('QR_CODE_SIZE', 256),
    'margin' => env('QR_CODE_MARGIN', 4),
    'error_correction' => env('QR_CODE_EC', 'M'), // L, M, Q, H
    'disk' => env('QR_CODE_DISK', 'public'),
    'path_prefix' => env('QR_CODE_PATH_PREFIX', 'qrcodes'),
    'encode_as_signed_url' => env('QR_CODE_SIGNED_URL', true),

    /*
    |--------------------------------------------------------------------------
    | Base URL untuk QR Code
    |--------------------------------------------------------------------------
    |
    | URL yang ditanam ke dalam QR Code. Nilai ini WAJIB bisa dijangkau HP
    | pelanggan dari Wi-Fi kafe, bukan "localhost" (localhost di HP akan
    | mengarah ke HP itu sendiri, jadi QR jadi tidak berguna).
    |
    | Kosongkan untuk memakai host dari request admin yang sedang membuka
    | halaman QR. Kalau admin membuka dashboard lewat IP LAN, QR otomatis
    | memakai IP LAN itu tanpa perlu konfigurasi.
    |
    | Contoh: 'http://192.168.1.100:8000'
    |
    */

    'base_url' => rtrim((string) env('QR_CODE_BASE_URL', ''), '/'),

    /**
     * BaconQrCode hanya menghasilkan SVG, jadi PNG dibuat lewat ImageMagick CLI.
     * Fallback dipakai bila path di atas tidak tersedia.
     */
    'magick_path' => env('MAGICK_PATH', '/usr/bin/magick'),
    'magick_fallbacks' => ['/usr/bin/magick', '/usr/bin/convert', 'magick', 'convert'],
];
