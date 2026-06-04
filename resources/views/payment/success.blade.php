@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h1>
            <p class="text-gray-600 mb-6">Terima kasih, pembayaran Anda telah diterima.</p>

            <!-- Order Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <div class="flex justify-between mb-3">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-semibold text-gray-800">#{{ $order->id_order }}</span>
                </div>
                <div class="flex justify-between mb-3">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-semibold text-gray-800">{{ $order->nama_pelanggan }}</span>
                </div>
                <div class="flex justify-between mb-3">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-semibold text-green-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Paid</span>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-800">
                    Pesanan Anda telah dikonfirmasi. Invoice dan rincian pembayaran telah dikirim ke email Anda.
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <a href="{{ route('menu.index') }}" class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    Lanjut Belanja
                </a>
                <a href="{{ route('order.show', ['order' => $order->id_order]) }}" class="block w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Lihat Pesanan
                </a>
            </div>

            <!-- Contact Support -->
            <p class="text-xs text-gray-500 mt-6">
                Butuh bantuan? <a href="#" class="text-green-600 hover:underline">Hubungi kami</a>
            </p>
        </div>
    </div>
</div>
@endsection
