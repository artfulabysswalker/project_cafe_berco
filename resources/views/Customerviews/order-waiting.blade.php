@extends('Customerviews.layouts.web')

@section('title', 'Pesanan Diterima - Berco Cafe')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-stone-100">
    <main class="container" style="max-width: 620px; margin: 0 auto; padding: 40px 16px;">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-6 py-8 text-center border-b border-stone-100 bg-emerald-50">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-600 text-white text-3xl shadow-lg">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="mt-4 text-2xl font-serif font-semibold text-stone-900">
                    Pesanan Diterima
                </h1>
                <p class="mt-1 text-sm text-stone-500">
                    @if($order->table)
                        Untuk <strong>{{ $order->table->nama_meja }}</strong>
                    @else
                        Pesanan take-away
                    @endif
                </p>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 px-4 py-4 text-center">
                    <p class="text-xs uppercase tracking-widest text-amber-700">Nomor Antrean / Kode Tagihan</p>
                    <p class="mt-1 font-mono text-3xl font-bold text-amber-800 tracking-widest">{{ $order->public_code }}</p>
                    <p class="mt-2 text-xs text-amber-700">
                        Sebutkan kode ini saat membayar di kasir.
                    </p>
                </div>

                <div class="rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
                    <div class="flex justify-between"><span>Pembayaran</span><span class="font-semibold text-stone-800">Bayar di Kasir (Cash)</span></div>
                    <div class="mt-1 flex justify-between"><span>Total</span><span class="font-semibold text-stone-800">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span></div>
                </div>

                <div class="space-y-2">
                    <h2 class="text-sm font-semibold text-stone-700">Detail Pesanan</h2>
                    <ul class="divide-y divide-stone-100 rounded-lg border border-stone-200 bg-white text-sm text-stone-600">
                        @foreach($order->items as $item)
                            <li class="flex items-start justify-between gap-3 px-4 py-2.5">
                                <div>
                                    <span class="font-semibold text-stone-800">{{ $item->quantity }}x</span>
                                    {{ $item->menu?->nama_menu ?? 'Item' }}
                                    @if($item->notes)
                                        <div class="mt-0.5 text-xs text-amber-700">📝 {{ $item->notes }}</div>
                                    @endif
                                </div>
                                <span class="shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="space-y-3 pt-1">
                    <a href="{{ route('order.show', $order->id_order) }}"
                       class="block w-full rounded-lg bg-terracotta px-4 py-3 text-center font-semibold text-white hover:bg-terracotta-dark transition-colors">
                        <i class="fas fa-truck-fast mr-1"></i> Lacak Status Pesanan
                    </a>
                    <a href="{{ route('menu.index', $order->table ? ['meja' => $order->table->nama_meja] : []) }}"
                       class="block w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-center text-sm font-semibold text-stone-600 hover:bg-stone-50 transition-colors">
                        <i class="fas fa-utensils mr-1"></i> Pesan Lagi
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection