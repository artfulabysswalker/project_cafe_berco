<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReceiptSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Show receipt settings form
     */
    public function edit()
    {
        $settings = ReceiptSetting::first();

        if (! $settings) {
            $settings = ReceiptSetting::create([
                'cafe_name' => 'BERCO CAFE',
                'address' => 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi',
                'phone' => '+62 821 4103 1234',
                'footer_message' => 'Terima kasih atas kunjungan Anda! Nikmati setiap seduhan kopi specialty Berco.',
                'wifi_name' => 'BERCO_CAFE_GUEST',
                'wifi_password' => 'kopiberco123',
            ]);
        }

        return view('admin.receipt.edit', compact('settings'));
    }

    /**
     * Update receipt settings
     */
    public function update(Request $request)
    {
        $settings = ReceiptSetting::first();

        if (! $settings) {
            $settings = new ReceiptSetting;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $settings->logo = $path;
        }

        $settings->cafe_name = $request->cafe_name ?? 'BERCO CAFE';
        $settings->address = $request->address;
        $settings->phone = $request->phone;
        $settings->footer_message = $request->footer_message;
        $settings->wifi_name = $request->wifi_name;
        $settings->wifi_password = $request->wifi_password;
        $settings->save();

        return back()->with('success', 'Pengaturan struk berhasil diperbarui.');
    }

    /**
     * View receipt preview for an order
     */
    public function view($id)
    {
        $order = Order::with(['items.menu', 'user'])->findOrFail($id);
        $settings = ReceiptSetting::first() ?? new ReceiptSetting([
            'cafe_name' => 'BERCO CAFE',
            'address' => 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi',
            'phone' => '+62 821 4103 1234',
            'footer_message' => 'Terima kasih atas kunjungan Anda!',
        ]);

        return view('admin.receipt.preview', compact('order', 'settings'));
    }

    /**
     * Print thermal receipt for an order
     */
    public function print($id)
    {
        $order = Order::with(['items.menu', 'user'])->findOrFail($id);
        $settings = ReceiptSetting::first() ?? new ReceiptSetting([
            'cafe_name' => 'BERCO CAFE',
            'address' => 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi',
            'phone' => '+62 821 4103 1234',
            'footer_message' => 'Terima kasih atas kunjungan Anda!',
        ]);

        return view('admin.receipt.print', compact('order', 'settings'));
    }

    /**
     * Download receipt as PDF
     */
    public function pdf($id)
    {
        $order = Order::with(['items.menu', 'user'])->findOrFail($id);
        $settings = ReceiptSetting::first() ?? new ReceiptSetting([
            'cafe_name' => 'BERCO CAFE',
            'address' => 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi',
            'phone' => '+62 821 4103 1234',
            'footer_message' => 'Terima kasih atas kunjungan Anda!',
        ]);

        $pdf = Pdf::loadView('admin.receipt.pdf', compact('order', 'settings'))
            ->setPaper([0, 0, 226.77, 650], 'portrait'); // 80mm thermal receipt roll size

        return $pdf->download('Struk-Order-'.$order->id_order.'.pdf');
    }
}
