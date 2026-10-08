<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\TableQrCodeException;
use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

/**
 * Titik masuk pelanggan setelah memindai QR meja.
 *
 * Halaman yang dirender sama persis dengan halaman order
 * (GET /order?token=...) sehingga semua QR mengarah ke satu
 * halaman order yang sama, dengan token sebagai parameter.
 */
class TableQrScanController extends Controller
{
    public function __construct(protected QrCodeService $qrService)
    {
        //
    }

    public function scan(Request $request, string $token)
    {
        $this->assertSignatureValid($request);

        return $this->respond($request, $token);
    }

    public function scanPayload(Request $request)
    {
        $payload = (string) $request->query('payload', '');

        if ($payload === '') {
            abort(404, 'QR Code tidak valid');
        }

        try {
            $data = $this->qrService->decryptPayload($payload);
        } catch (TableQrCodeException $e) {
            abort(404, $e->getMessage());
        }

        if (! isset($data['token']) || ! is_string($data['token'])) {
            abort(404, 'QR Code tidak valid');
        }

        return $this->respond($request, $data['token']);
    }

    /**
     * Meloloskan request tanpa signature hanya jika aplikasi dikonfigurasi
     * memakai encrypted payload (bukan signed URL).
     */
    private function assertSignatureValid(Request $request): void
    {
        if (config('qrcode.encode_as_signed_url', true)) {
            if (! $request->hasValidSignature()) {
                abort(403, 'Signature QR Code tidak valid');
            }
        }
    }

    /**
     * Validasi token + meja aktif, simpan identitas meja di session,
     * lalu tampilkan halaman order (sama dengan /order?token=...).
     */
    private function respond(Request $request, string $token)
    {
        try {
            $qr = $this->qrService->validateTokenForOrder($token);
        } catch (TableQrCodeException $e) {
            return response()->view('Customerviews.order-error', [
                'errorMessage' => $e->getMessage().', silakan hubungi kasir.',
            ], 404);
        }

        $table = $qr->table;

        $request->session()->put('order_table_token', $qr->token);
        $request->session()->put('order_table', $table->nama_meja);
        $request->session()->put('order_table_id', $table->id_meja);

        return view('Customerviews.order', [
            'table' => $table,
            'qr' => $qr,
            'cartCount' => (int) (auth()->user()?->cartItems()->sum('quantity') ?? 0),
        ]);
    }
}
