<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TableQrCodeException;
use App\Http\Controllers\Controller;
use App\Models\Meja;
use App\Models\Order;
use App\Models\TableQrCode;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TableQrCodeController extends Controller
{
    public function __construct(protected QrCodeService $qrService)
    {
        //
    }

    public function index(Request $request)
    {
        $mejas = Meja::with(['qrCodes' => function ($q) {
            $q->latest();
        }])->orderBy('nama_meja')->paginate(20);

        return view('admin.tables.index', compact('mejas'));
    }

    public function show($tableId)
    {
        $table = Meja::findOrFail($tableId);
        $qr = $this->latestQr($table);

        return view('admin.tables.qrcode', [
            'table' => $table,
            'qr' => $qr,
            'imageExists' => $qr && $this->qrService->imageExists($qr),
            'payload' => $qr ? $this->qrService->payloadFor($qr) : null,
            'scanUrl' => $qr ? $this->qrService->scanUrlFor($qr) : null,
        ]);
    }

    public function generate(Request $request, $tableId)
    {
        $request->validate([
            'expires_in_hours' => 'nullable|integer|min:0|max:720',
        ]);

        try {
            $qr = $this->qrService->regenerate(
                $tableId,
                $request->filled('expires_in_hours') ? (int) $request->input('expires_in_hours') : null
            );
        } catch (TableQrCodeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.tables.qrcode', $tableId)
            ->with('success', 'QR Code berhasil digenerate');
    }

    public function revoke(Request $request, $tableId)
    {
        $table = Meja::findOrFail($tableId);
        try {
            $this->qrService->revokeAllForTable($table->id_meja);
        } catch (TableQrCodeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.tables.qrcode', $tableId)
            ->with('success', 'QR Code berhasil di-revoke');
    }

    /**
     * Stream PNG untuk ditampilkan di halaman admin.
     *
     * Sengaja tidak memakai Storage::url() supaya file QR tidak terekspos
     * di path publik: PNG memuat signed URL yang berfungsi sebagai kredensial.
     */
    public function image(Request $request, $tableId)
    {
        $table = Meja::findOrFail($tableId);
        $qr = $this->latestQr($table);

        if (! $qr) {
            abort(404);
        }

        return $this->pngResponse($qr, 'inline');
    }

    public function download($tableId)
    {
        $table = Meja::findOrFail($tableId);
        $qr = $this->latestQr($table);

        if (! $qr) {
            return back()->withErrors(['error' => 'QR Code belum digenerate']);
        }

        return $this->pngResponse($qr, 'attachment', sprintf(
            'qr-meja-%s-%s.png',
            Str::slug($table->nama_meja),
            $qr->token
        ));
    }

    /**
     * Riwayat pesanan untuk satu meja (filter admin per meja/token).
     */
    public function orders($tableId)
    {
        $table = Meja::findOrFail($tableId);

        $orders = Order::where('table_id', $table->id_meja)
            ->with(['user', 'items.menu'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.tables.orders', compact('table', 'orders'));
    }

    /**
     * Halaman siap cetak QR untuk ditempel di meja.
     */
    public function printQr($tableId)
    {
        $table = Meja::findOrFail($tableId);
        $qr = $this->latestQr($table);

        if (! $qr) {
            return redirect()->route('admin.tables.qrcode', $tableId)
                ->withErrors(['error' => 'Generate QR Code terlebih dahulu']);
        }

        return view('admin.tables.print', [
            'table' => $table,
            'qr' => $qr,
            'imageExists' => $this->qrService->imageExists($qr),
            'scanUrl' => $this->qrService->scanUrlFor($qr),
        ]);
    }

    private function latestQr(Meja $table): ?TableQrCode
    {
        return TableQrCode::forTable($table->id_meja)->latest()->first();
    }

    private function pngResponse(TableQrCode $qr, string $disposition, ?string $filename = null)
    {
        $relativePath = $this->qrService->imagePath($qr);
        $disk = Storage::disk(config('qrcode.disk', 'public'));

        if (! $disk->exists($relativePath)) {
            abort(404, 'File QR Code tidak ditemukan');
        }

        return response(
            $disk->get($relativePath),
            200,
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => sprintf(
                    '%s; filename="%s"',
                    $disposition,
                    $filename ?? basename($relativePath)
                ),
                'Content-Length' => (string) $disk->size($relativePath),
                'Cache-Control' => 'no-store, private',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
