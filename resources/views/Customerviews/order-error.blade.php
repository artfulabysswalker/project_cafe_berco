@extends('Customerviews.layouts.web')

@section('title', 'QR Tidak Valid - Berco Cafe')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-stone-100">
    <main class="container" style="max-width: 560px; margin: 0 auto; padding: 60px 16px;">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden text-center">
            <div class="px-6 py-10">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50 border border-red-100">
                    <i class="fas fa-qrcode text-2xl text-red-500"></i>
                </div>

                <h1 class="mt-5 text-2xl font-bold text-stone-900">QR Tidak Valid</h1>

                <p class="mt-3 text-sm text-stone-600">
                    {{ $errorMessage }}
                </p>

                <p class="mt-2 text-xs text-stone-400">
                    Pastikan Anda memindai QR Code yang terpasang di meja Anda.
                </p>

                <div class="mt-6">
                    <a href="{{ route('home') }}"
                       class="block w-full rounded-lg bg-terracotta px-4 py-3 font-semibold text-white hover:bg-terracotta-dark transition-colors">
                        <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-stone-400">
            Mohon maaf atas ketidaknyamanannya. Kasir dengan senang hati membantu Anda.
        </p>
    </main>
</div>
@endsection
