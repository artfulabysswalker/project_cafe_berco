@extends('Customerviews.layouts.web')

@section('title', 'Beranda Member — Cafe Berco')

@section('content')
<div class="space-y-8">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md text-xs font-mono flex items-center space-x-2">
            <i class="fas fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->has('daily'))
        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-md text-xs font-mono flex items-center space-x-2">
            <i class="fas fa-triangle-exclamation text-amber-600"></i>
            <span>{{ $errors->first('daily') }}</span>
        </div>
    @endif

    {{-- Hero Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        {{-- Welcome Banner --}}
        <div class="lg:col-span-7 bg-white border border-border rounded-lg p-6 flex flex-col justify-between space-y-6 shadow-2xs">
            <div class="space-y-3">
                <div class="inline-flex items-center space-x-2 text-[11px] font-mono uppercase tracking-widest text-terracotta">
                    <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                    <span>Loyalty & Member Portal</span>
                </div>
                <h1 class="text-3xl font-serif text-ink tracking-tight font-normal">
                    Selamat datang, {{ Auth::user()->name }}
                </h1>
                <p class="text-xs text-ink-muted leading-relaxed">
                    Nikmati racikan kopi specialty pilihan, selesaikan quest harian, kumpulkan EXP, dan tukarkan dengan voucher diskon serta menu gratis.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 pt-2">
                <a href="{{ route('menu.index') }}" class="text-xs font-mono font-medium bg-ink text-white px-4 py-2 rounded-md hover:bg-stone-800 transition-colors shadow-2xs inline-flex items-center space-x-1.5">
                    <i class="fas fa-mug-hot text-[10px]"></i>
                    <span>Jelajahi Menu</span>
                </a>
                <a href="{{ route('redeem.index') }}" class="text-xs font-mono font-medium bg-white text-ink border border-border px-4 py-2 rounded-md hover:bg-stone-50 transition-colors inline-flex items-center space-x-1.5">
                    <i class="fas fa-gift text-terracotta text-[10px]"></i>
                    <span>Tukar Hadiah</span>
                </a>
                <a href="{{ route('playlists.index') }}" class="text-xs font-mono text-ink-muted hover:text-ink border border-border px-3.5 py-2 rounded-md hover:bg-stone-50 transition-colors">
                    <i class="fas fa-music text-[10px] mr-1"></i> Request Musik
                </a>
            </div>
        </div>

        {{-- EXP & Daily Streak Card --}}
        <div class="lg:col-span-5 bg-white border border-border rounded-lg p-6 flex flex-col justify-between space-y-5 shadow-2xs">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div>
                    <span class="text-[10px] font-mono text-ink-muted uppercase tracking-widest block">STATUS MEMBER</span>
                    <h3 class="text-sm font-semibold text-ink">{{ Auth::user()->is_guest ? 'Guest Customer' : 'Berco Tier Member' }}</h3>
                </div>
                <div class="text-right font-mono">
                    <span class="text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        {{ number_format(Auth::user()->exp ?? 0) }} EXP
                    </span>
                </div>
            </div>

            {{-- Daily Streak Box --}}
            <div class="bg-canvas border border-border/80 rounded-md p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-semibold text-ink">Daily Streak Bonus</h4>
                        <p class="text-[11px] text-ink-muted mt-0.5">Klaim <strong class="text-ink">+50 EXP</strong> gratis setiap 24 jam.</p>
                    </div>
                </div>

                @php
                    $hasClaimedToday = Auth::user()->last_daily_claim && Auth::user()->last_daily_claim->isToday();
                @endphp

                @if(!$hasClaimedToday)
                    <form method="POST" action="{{ route('daily.claim') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full text-xs font-mono bg-terracotta hover:bg-terracotta-dark text-white py-2 rounded-md transition-colors shadow-2xs">
                            Klaim 50 EXP Sekarang
                        </button>
                    </form>
                @else
                    <button type="button" class="w-full text-xs font-mono bg-stone-100 text-stone-500 py-2 rounded-md cursor-not-allowed border border-border" disabled>
                        ✓ Sudah Diklaim Hari Ini
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-[11px] font-mono border-t border-stone-100 pt-3 text-ink-muted">
                <a href="{{ route('redeem.index') }}" class="hover:text-ink">Voucher →</a>
                <a href="{{ route('daily.quest') }}" class="hover:text-ink">Quest →</a>
                <a href="{{ route('order.history') }}" class="hover:text-ink">Riwayat →</a>
            </div>
        </div>

    </div>

    {{-- Features Grid --}}
    <div class="space-y-4">
        <div class="border-b border-border pb-2">
            <h2 class="text-xs font-mono uppercase tracking-widest text-ink-muted font-semibold">Layanan & Menu Cepat</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Feature 1: Menu --}}
            <a href="{{ route('menu.index') }}" class="bg-white border border-border rounded-lg p-5 hover:border-stone-400 transition-colors flex flex-col justify-between space-y-3 group shadow-2xs">
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-md bg-stone-100 text-ink flex items-center justify-center text-xs">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-ink group-hover:text-terracotta transition-colors">Daftar Menu Spesial</h3>
                    <p class="text-xs text-ink-muted leading-relaxed">Pilih aneka kopi espresso, manual brew, minuman non-kopi, dan kudapan segar.</p>
                </div>
                <span class="text-xs font-mono text-ink font-medium">Buka Menu →</span>
            </a>

            {{-- Feature 2: Tukar EXP --}}
            <a href="{{ route('redeem.index') }}" class="bg-white border border-border rounded-lg p-5 hover:border-stone-400 transition-colors flex flex-col justify-between space-y-3 group shadow-2xs">
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-md bg-stone-100 text-ink flex items-center justify-center text-xs">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-ink group-hover:text-terracotta transition-colors">Tukar EXP & Voucher</h3>
                    <p class="text-xs text-ink-muted leading-relaxed">Gunakan EXP Anda untuk ditukar voucher diskon dan minuman gratis.</p>
                </div>
                <span class="text-xs font-mono text-ink font-medium">Tukar Hadiah →</span>
            </a>

            {{-- Feature 3: Daily Quest --}}
            <a href="{{ route('daily.quest') }}" class="bg-white border border-border rounded-lg p-5 hover:border-stone-400 transition-colors flex flex-col justify-between space-y-3 group shadow-2xs">
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-md bg-stone-100 text-ink flex items-center justify-center text-xs">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-ink group-hover:text-terracotta transition-colors">Daily Quest & Streak</h3>
                    <p class="text-xs text-ink-muted leading-relaxed">Selesaikan misi belanja, unlock achievement, dan raih reward ekstra.</p>
                </div>
                <span class="text-xs font-mono text-ink font-medium">Lihat Quest →</span>
            </a>

            {{-- Feature 4: Playlist --}}
            <a href="{{ route('playlists.index') }}" class="bg-white border border-border rounded-lg p-5 hover:border-stone-400 transition-colors flex flex-col justify-between space-y-3 group shadow-2xs">
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-md bg-stone-100 text-ink flex items-center justify-center text-xs">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-ink group-hover:text-terracotta transition-colors">Playlist Kafe</h3>
                    <p class="text-xs text-ink-muted leading-relaxed">Request lagu favorit Anda untuk diputar di kafe dan vote bersama pelanggan lain.</p>
                </div>
                <span class="text-xs font-mono text-ink font-medium">Request Musik →</span>
            </a>

            {{-- Feature 5: Cart --}}
            <a href="{{ route('cart.index') }}" class="bg-white border border-border rounded-lg p-5 hover:border-stone-400 transition-colors flex flex-col justify-between space-y-3 group shadow-2xs">
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-md bg-stone-100 text-ink flex items-center justify-center text-xs">
                        <i class="fas fa-bag-shopping"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-ink group-hover:text-terracotta transition-colors">Keranjang Pesanan</h3>
                    <p class="text-xs text-ink-muted leading-relaxed">Cek item pesanan Anda, pilih Dine In atau Take Away, lalu bayar via QRIS/Cash.</p>
                </div>
                <span class="text-xs font-mono text-ink font-medium">Buka Keranjang →</span>
            </a>

            {{-- Feature 6: History --}}
            <a href="{{ route('order.history') }}" class="bg-white border border-border rounded-lg p-5 hover:border-stone-400 transition-colors flex flex-col justify-between space-y-3 group shadow-2xs">
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-md bg-stone-100 text-ink flex items-center justify-center text-xs">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-ink group-hover:text-terracotta transition-colors">Riwayat & Struk</h3>
                    <p class="text-xs text-ink-muted leading-relaxed">Pantau status pesanan dan unduh struk digital bukti transaksi Anda.</p>
                </div>
                <span class="text-xs font-mono text-ink font-medium">Cek Riwayat →</span>
            </a>
        </div>
    </div>
</div>
@endsection
