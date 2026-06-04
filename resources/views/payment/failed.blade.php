@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Error Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Gagal</h1>
            <p class="text-gray-600 mb-6">Maaf, pembayaran tidak dapat diproses.</p>

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
                    <span class="font-semibold text-red-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Failed</span>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-red-800">
                    Pembayaran Anda telah ditolak atau kadaluarsa. Silakan coba lagi atau gunakan metode pembayaran lain.
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <a href="{{ route('xendit.payment.redirect', ['order' => $order->id_order]) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    Coba Pembayaran Lagi
                </a>
                <a href="{{ route('payment.show', ['order' => $order->id_order]) }}" class="block w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Kembali ke Pembayaran
                </a>
                <a href="{{ route('menu.index') }}" class="block w-full px-4 py-2 border border-gray-300 text-gray-800 rounded-lg hover:bg-gray-50 transition font-semibold">
                    Kembali ke Menu
                </a>
            </div>

            <!-- Contact Support -->
            <p class="text-xs text-gray-500 mt-6">
                Masalah persisten? <a href="#" class="text-blue-600 hover:underline">Hubungi support</a>
            </p>
        </div>
    </div>
</div>
@endsection
