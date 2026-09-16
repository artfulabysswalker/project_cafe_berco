<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceiptSetting;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
   public function edit()
   {
       $settings = ReceiptSetting::first();

       if (!$settings) {
           $settings = ReceiptSetting::create([]);
       }

       return view('admin.receipt.edit', compact('settings')); // ✅ fixed
   }

   public function update(Request $request)
   {
       $settings = ReceiptSetting::first();

       if ($request->hasFile('logo')) {
           $path = $request->file('logo')->store('logos', 'public');
           $settings->logo = $path;
       }

       $settings->update([
           'cafe_name' => $request->cafe_name,
           'address' => $request->address,
           'phone' => $request->phone,
           'footer_message' => $request->footer_message,
           'wifi_name' => $request->wifi_name,
           'wifi_password' => $request->wifi_password,
           'logo' => $settings->logo,
       ]);

       return back()->with('success', 'Updated!');
   }

   public function print($id)
   {
       $order = Order::with('items.menu')->findOrFail($id);
       $settings = ReceiptSetting::first();

       return view('admin.receipt.print', compact('order', 'settings')); // ✅ fixed
   }

public function pdf(Request $request)
{
    $file = $request->file;

    return response()->download(storage_path('app/' . $file));
}
public function view($id)
{
    $order = Order::with('items.menu')
        ->where('id_order', $id)
        ->firstOrFail();

    $settings = ReceiptSetting::first();

    return view('admin.receipt.preview', compact('order', 'settings'));
}

public function viewHistory($id)
{
    $order = OrderHistory::where('id_order', $id)
        ->firstOrFail();

    $settings = ReceiptSetting::first();

    return view('admin.receipt.preview', compact('order', 'settings'));
}
}