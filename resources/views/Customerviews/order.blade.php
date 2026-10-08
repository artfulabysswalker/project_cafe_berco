@extends('Customerviews.layouts.web')

@section('title', 'Halaman Order - Berco Cafe')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-stone-100">
    <main class="container" style="max-width: 640px; margin: 0 auto; padding: 40px 16px;">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-6 py-8 text-center border-b border-stone-100 bg-stone-50">
                <p class="text-xs uppercase tracking-widest text-stone-500">Selamat datang di</p>
                <h1 class="mt-1 text-3xl font-serif font-semibold text-stone-900">
                    Berco<span class="text-terracotta">.</span> Cafe
                </h1>

                <div class="mt-5 inline-flex items-center gap-2 rounded-full bg-terracotta/10 border border-terracotta/30 px-5 py-2">
                    <i class="fas fa-chair text-terracotta"></i>
                    <span class="text-lg font-bold text-stone-900">
                        {{ $table->nama_meja }}
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-xs text-stone-500">
                    @if($table->kapasitas)
                        <span><i class="fas fa-users"></i> Kapasitas {{ $table->kapasitas }} orang</span>
                    @endif
                    @if($table->area)
                        <span><i class="fas fa-map-marker-alt"></i> Area {{ $table->area }}</span>
                    @endif
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Meja aktif
                    </span>
                </div>

                @if($qr && $qr->expired_at)
                    <p class="mt-3 text-[11px] text-stone-400">
                        Berlaku sampai {{ $qr->expired_at->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>

            <div class="px-6 py-6 space-y-3">
                <p class="text-sm text-stone-600 text-center">
                    Pesanan Anda akan tercatat untuk <strong>{{ $table->nama_meja }}</strong>.
                    Pilih menu untuk mulai memesan.
                </p>

                <a href="{{ route('menu.index', ['meja' => $table->nama_meja]) }}"
                   class="block w-full rounded-lg bg-terracotta px-4 py-3 text-center font-semibold text-white hover:bg-terracotta-dark transition-colors">
                    <i class="fas fa-utensils mr-1"></i> Lihat Menu &amp; Pesan
                </a>

                @if(($cartCount ?? 0) > 0)
                    <a href="{{ route('checkout') }}"
                       class="block w-full rounded-lg bg-emerald-600 px-4 py-3 text-center font-semibold text-white hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-arrow-right mr-1"></i> Lanjut ke Pembayaran ({{ $cartCount }} item)
                    </a>
                @endif

                <a href="{{ route('cart.index') }}"
                   class="block w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-center text-sm font-semibold text-stone-600 hover:bg-stone-50 transition-colors">
                    <i class="fas fa-bag-shopping mr-1"></i> Lihat Keranjang
                </a>
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-stone-400">
            QR Code ini khusus untuk meja Anda. Ada masalah? Hubungi kasir.
        </p>
    </main>
</div>
@endsection
