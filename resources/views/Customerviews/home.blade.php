@extends('Customerviews.layouts.web')

@section('title', 'Beranda - Berco Cafe')

@section('content')
    <section class="hero-home" style="padding: 120px 40px 80px; background: linear-gradient(180deg, #fff9f1 0%, #fff1df 100%); min-height: calc(100vh - 180px);">
        <div class="max-w-6xl mx-auto">
            <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] items-start">
                <div>
                    <p class="text-sm uppercase tracking-[0.4em] text-[#c2410c] mb-4">Selamat datang di Berco Cafe</p>
                    <h1 class="text-5xl font-extrabold leading-tight text-[#3a1f0f] mb-6">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="text-lg text-[#5f3b22] mb-8">Nikmati menu terbaik kami, klaim hadiah harian, dan tukarkan EXP untuk rewards menarik.</p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <a href="{{ route('menu.index') }}" class="inline-flex items-center justify-center gap-3 rounded-3xl bg-[#c2410c] px-8 py-4 text-white text-base font-semibold shadow-xl shadow-[#c2410c]/20 transition hover:-translate-y-1">Pesan Sekarang <i class="fas fa-mug-hot"></i></a>
                        <a href="{{ route('cart.index') }}" class="inline-flex items-center justify-center gap-3 rounded-3xl bg-white border border-[#ddb892] px-8 py-4 text-[#6b3a0f] text-base font-semibold shadow-sm transition hover:-translate-y-1">Lihat Keranjang <i class="fas fa-shopping-cart"></i></a>
                    </div>
                </div>

                <div class="status-card" style="background: white; border-radius: 32px; padding: 36px; box-shadow: 0 30px 80px rgba(67, 32, 12, 0.12);">
                    <h2 class="text-2xl font-bold text-[#5f3b22] mb-4">Status Akun</h2>

                    <div class="stats grid gap-4">
                        <div class="stat-item" style="background: #fff4e6; border-radius: 24px; padding: 24px;">
                            <p class="text-sm uppercase tracking-[0.3em] text-[#b45309] mb-2">EXP Anda</p>
                            <p class="text-4xl font-extrabold text-[#92400e]">{{ Auth::user()->exp ?? 0 }}</p>
                        </div>

                        <div class="stat-item" style="background: #fdf3e6; border-radius: 24px; padding: 24px;">
                            <p class="text-sm uppercase tracking-[0.3em] text-[#b45309] mb-2">Akun</p>
                            <p class="text-lg font-semibold text-[#7c4a24]">{{ Auth::user()->is_guest ? 'Guest' : 'Terdaftar' }}</p>
                        </div>

                        <div class="stat-item" style="background: #eef7ff; border-radius: 24px; padding: 24px;">
                            <p class="text-sm uppercase tracking-[0.3em] text-[#1d4ed8] mb-2">Daily Streak</p>
                            @php
                                $lastClaim = Auth::user()->last_daily_claim;
                                $streakMessage = 'Belum dimulai. Klaim sekarang untuk memulai streak!';

                                if ($lastClaim) {
                                    if ($lastClaim->isToday()) {
                                        $streakMessage = 'Streak aktif: kamu sudah klaim hari ini.';
                                    } elseif ($lastClaim->isYesterday()) {
                                        $streakMessage = 'Streak hampir lanjut, klaim hari ini untuk menjaga streak.';
                                    } else {
                                        $streakMessage = 'Mulai streak baru dengan klaim harian berikutnya.';
                                    }
                                }
                            @endphp
                            <p class="text-sm text-[#1e3a8a]">{{ $streakMessage }}</p>
                        </div>

                        <div class="stat-item" style="background: #f7f3ee; border-radius: 24px; padding: 24px;">
                            <p class="text-sm uppercase tracking-[0.3em] text-[#b45309] mb-2">Daily Claim</p>
                            @if(!Auth::user()->last_daily_claim || !Auth::user()->last_daily_claim->isToday())
                                <form method="POST" action="{{ route('daily.claim') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-3xl bg-[#f59e0b] px-5 py-3 text-white font-semibold shadow hover:bg-[#d97706] transition">Klaim Sekarang</button>
                                </form>
                            @else
                                <p class="text-sm text-[#6b4226]">Sudah diklaim hari ini. Kembali besok untuk bonus lagi.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <a href="{{ route('daily-quest') }}" class="block rounded-3xl bg-[#fff1d6] px-6 py-4 text-[#9a3412] font-semibold transition hover:bg-[#ffe8c2]">Daily Quest</a>
                        <a href="{{ route('redeem.index') }}" class="block rounded-3xl bg-[#f8fafc] border border-[#f1f5f9] px-6 py-4 text-[#475569] font-semibold transition hover:bg-[#eef2ff]">Tukar EXP</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
