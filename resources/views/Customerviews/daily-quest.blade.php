@extends('Customerviews.layouts.web')

@section('title', 'Daily Quest & Streak - Berco Cafe')

@section('content')
<div class="quest-minimal-page">
    <div class="quest-max-container">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-minimal alert-success-min">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->has('daily'))
            <div class="alert-minimal alert-warning-min">
                <i class="fas fa-circle-exclamation"></i> {{ $errors->first('daily') }}
            </div>
        @endif

        {{-- 1. COMPACT STREAK & EXP CLAIM BAR --}}
        @php
            $hasClaimedToday = auth()->user()->last_daily_claim && auth()->user()->last_daily_claim->isToday();
            $userExp = auth()->user()->exp ?? 0;
        @endphp

        <div class="streak-banner-card">
            <div class="streak-left-info">
                <div class="streak-icon-box">
                    <i class="fas fa-fire-flame-curved"></i>
                </div>
                <div>
                    <div class="streak-title-row">
                        <h1 class="streak-title-text">Daily Streak & Loyalty Quest</h1>
                        <span class="pill-daily-status {{ $hasClaimedToday ? 'claimed' : 'unclaimed' }}">
                            {{ $hasClaimedToday ? '🟢 Streak Aktif' : '⚡ Klaim Tersedia' }}
                        </span>
                    </div>
                    <p class="streak-sub-text">Check-in setiap hari untuk menjaga streak dan kumpulkan EXP gratis untuk ditukar reward.</p>
                </div>
            </div>

            <div class="streak-claim-action-box">
                <div class="exp-inline-display">
                    <span class="exp-lbl">Saldo Anda:</span>
                    <strong class="exp-val">{{ number_format($userExp) }} EXP</strong>
                </div>

                @if(!$hasClaimedToday)
                    <form method="POST" action="{{ route('daily.claim') }}">
                        @csrf
                        <button type="submit" class="btn-claim-streak">
                            <i class="fas fa-gift"></i> Klaim +50 EXP Hari Ini
                        </button>
                    </form>
                @else
                    <div class="streak-claimed-badge">
                        <i class="fas fa-check-circle"></i> Sudah Diklaim Hari Ini
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. DAILY QUESTS (MINIMALIST LIST LAYOUT) --}}
        <div class="quest-section-card">
            <div class="quest-section-header">
                <div>
                    <h2 class="section-heading-text"><i class="fas fa-list-check text-amber"></i> Tantangan Harian (Daily Quest)</h2>
                    <p class="section-sub-text">Selesaikan quest di bawah ini untuk mendapatkan bonus EXP dan voucher spesial.</p>
                </div>
                <a href="{{ route('menu.index') }}" class="btn-goto-menu">
                    <i class="fas fa-mug-hot"></i> Buka Menu
                </a>
            </div>

            <div class="minimal-quest-list">
                {{-- Quest 1 --}}
                <div class="quest-list-row">
                    <div class="quest-row-icon bg-amber-soft">
                        <i class="fas fa-mug-hot text-amber"></i>
                    </div>
                    <div class="quest-row-content">
                        <div class="quest-row-header">
                            <h3 class="quest-name">Beli 2 Varian Kopi Hari Ini</h3>
                            <span class="quest-reward-pill">+50 EXP</span>
                        </div>
                        <p class="quest-desc">Pesan minimal 2 cup kopi specialty dalam satu pesanan.</p>
                        <div class="quest-progress-wrap">
                            <div class="quest-progress-bar"><div class="quest-progress-fill" style="width: 50%;"></div></div>
                            <span class="quest-progress-txt">1 / 2 Cup</span>
                        </div>
                    </div>
                    <div class="quest-row-action">
                        <a href="{{ route('menu.index') }}" class="btn-quest-action">Pesan</a>
                    </div>
                </div>

                {{-- Quest 2 --}}
                <div class="quest-list-row">
                    <div class="quest-row-icon bg-blue-soft">
                        <i class="fas fa-pen-to-square text-blue"></i>
                    </div>
                    <div class="quest-row-content">
                        <div class="quest-row-header">
                            <h3 class="quest-name">Beri Ulasan Menu Favorit</h3>
                            <span class="quest-reward-pill">+30 EXP</span>
                        </div>
                        <p class="quest-desc">Bagikan pengalaman dan ulasan Anda pada menu yang telah dinikmati.</p>
                        <div class="quest-progress-wrap">
                            <div class="quest-progress-bar"><div class="quest-progress-fill bg-blue" style="width: 0%;"></div></div>
                            <span class="quest-progress-txt">0 / 1 Ulasan</span>
                        </div>
                    </div>
                    <div class="quest-row-action">
                        <a href="{{ route('menu.index') }}#menu-review-form" class="btn-quest-action">Review</a>
                    </div>
                </div>

                {{-- Quest 3 --}}
                <div class="quest-list-row">
                    <div class="quest-row-icon bg-green-soft">
                        <i class="fas fa-basket-shopping text-green"></i>
                    </div>
                    <div class="quest-row-content">
                        <div class="quest-row-header">
                            <h3 class="quest-name">Belanja Minimal Rp 50.000</h3>
                            <span class="quest-reward-pill">+100 EXP</span>
                        </div>
                        <p class="quest-desc">Lengkapi pesanan Anda dan raih bonus loyalitas poin besar.</p>
                        <div class="quest-progress-wrap">
                            <div class="quest-progress-bar"><div class="quest-progress-fill bg-green" style="width: 70%;"></div></div>
                            <span class="quest-progress-txt">Rp 35.000 / Rp 50.000</span>
                        </div>
                    </div>
                    <div class="quest-row-action">
                        <a href="{{ route('menu.index') }}" class="btn-quest-action">Pesan</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BADGES & ACHIEVEMENTS (COMPACT LIST) --}}
        <div class="quest-section-card">
            <div class="quest-section-header">
                <div>
                    <h2 class="section-heading-text"><i class="fas fa-medal text-amber"></i> Lencana & Pencapaian (Badges)</h2>
                    <p class="section-sub-text">Koleksi lencana reputasi kamu sebagai pelanggan setia Berco Cafe.</p>
                </div>
                <a href="{{ route('redeem.index') }}" class="btn-goto-menu">
                    <i class="fas fa-gift"></i> Tukar Hadiah
                </a>
            </div>

            <div class="compact-badges-grid">
                <div class="compact-badge-item unlocked">
                    <div class="badge-mini-icon">☕</div>
                    <div class="badge-mini-info">
                        <strong class="badge-mini-title">First Sip</strong>
                        <p class="badge-mini-desc">Pesanan pertama berhasil dibuat.</p>
                        <span class="badge-mini-status"><i class="fas fa-check"></i> Terbuka</span>
                    </div>
                </div>

                <div class="compact-badge-item unlocked">
                    <div class="badge-mini-icon">🔥</div>
                    <div class="badge-mini-info">
                        <strong class="badge-mini-title">Daily Regular</strong>
                        <p class="badge-mini-desc">Aktif check-in 3 hari berturut-turut.</p>
                        <span class="badge-mini-status"><i class="fas fa-check"></i> Terbuka</span>
                    </div>
                </div>

                <div class="compact-badge-item locked">
                    <div class="badge-mini-icon">⭐</div>
                    <div class="badge-mini-info">
                        <strong class="badge-mini-title">Top Reviewer</strong>
                        <p class="badge-mini-desc">Tulis 5 ulasan menu Berco Cafe.</p>
                        <span class="badge-mini-status locked"><i class="fas fa-lock"></i> Terkunci</span>
                    </div>
                </div>

                <div class="compact-badge-item locked">
                    <div class="badge-mini-icon">👑</div>
                    <div class="badge-mini-info">
                        <strong class="badge-mini-title">Berco VIP</strong>
                        <p class="badge-mini-desc">Kumpulkan total 1.000 EXP poin.</p>
                        <span class="badge-mini-status locked"><i class="fas fa-lock"></i> Terkunci</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .quest-minimal-page {
        min-height: 100vh;
        background-color: var(--cream-bg);
        padding: 30px 20px 80px;
    }

    .quest-max-container {
        max-width: 1000px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .alert-minimal {
        padding: 12px 18px;
        border-radius: 14px;
        font-size: 13.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success-min {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .alert-warning-min {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }

    /* 1. STREAK BANNER CARD */
    .streak-banner-card {
        background: linear-gradient(135deg, #1C1008 0%, #2E190E 100%);
        border: 1px solid rgba(212, 117, 44, 0.25);
        border-radius: 20px;
        padding: 26px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        color: #FFFFFF;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        flex-wrap: wrap;
    }

    .streak-left-info {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1 1 380px;
    }

    .streak-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(245, 158, 11, 0.18);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #F59E0B;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .streak-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }

    .streak-title-text {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 800;
        color: #FFFFFF;
        margin: 0;
    }

    .pill-daily-status {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 999px;
    }

    .pill-daily-status.claimed {
        background: rgba(34, 197, 94, 0.2);
        color: #4ADE80;
        border: 1px solid rgba(34, 197, 94, 0.4);
    }

    .pill-daily-status.unclaimed {
        background: rgba(245, 158, 11, 0.2);
        color: #FBBF24;
        border: 1px solid rgba(245, 158, 11, 0.4);
    }

    .streak-sub-text {
        font-size: 13px;
        color: #D6C2B0;
        margin: 0;
        line-height: 1.45;
    }

    .streak-claim-action-box {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }

    .exp-inline-display {
        font-size: 12.5px;
        color: #A89280;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .exp-inline-display strong {
        color: #F59E0B;
        font-size: 16px;
    }

    .btn-claim-streak {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: #FFFFFF;
        border: none;
        padding: 10px 22px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.35);
        transition: transform 0.2s ease;
    }

    .btn-claim-streak:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.45);
    }

    .streak-claimed-badge {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #E2D3C5;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* 2. QUEST SECTION CARD */
    .quest-section-card {
        background: #FFFFFF;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }

    .quest-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid rgba(107, 63, 31, 0.08);
        flex-wrap: wrap;
        gap: 12px;
    }

    .section-heading-text {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0 0 2px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-sub-text {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 0;
    }

    .btn-goto-menu {
        background: #FAF4EB;
        border: 1px solid rgba(212, 117, 44, 0.2);
        color: var(--primary-dark);
        padding: 7px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-goto-menu:hover {
        background: var(--primary);
        color: #FFFFFF;
    }

    .minimal-quest-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Quest List Row */
    .quest-list-row {
        background: #FDFBF8;
        border: 1px solid #EFE6DC;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .quest-list-row:hover {
        background: #FFFFFF;
        border-color: rgba(212, 117, 44, 0.4);
        box-shadow: 0 4px 16px rgba(107, 63, 31, 0.06);
    }

    .quest-row-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .bg-amber-soft { background: #FEF3C7; }
    .bg-blue-soft { background: #EFF6FF; }
    .bg-green-soft { background: #DCFCE7; }
    .text-blue { color: #2563EB; }
    .text-green { color: #15803D; }

    .quest-row-content {
        flex: 1 1 300px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .quest-row-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quest-name {
        font-size: 14.5px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .quest-reward-pill {
        background: #FEF3C7;
        color: #B45309;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .quest-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin: 0;
    }

    .quest-progress-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 4px;
    }

    .quest-progress-bar {
        flex: 1;
        max-width: 200px;
        height: 6px;
        background: #E5E7EB;
        border-radius: 999px;
        overflow: hidden;
    }

    .quest-progress-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 999px;
    }

    .quest-progress-fill.bg-blue { background: #2563EB; }
    .quest-progress-fill.bg-green { background: #15803D; }

    .quest-progress-txt {
        font-size: 11px;
        font-weight: 700;
        color: #64748B;
    }

    .quest-row-action {
        flex-shrink: 0;
    }

    .btn-quest-action {
        background: var(--primary);
        color: #FFFFFF;
        padding: 7px 16px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background 0.2s ease;
    }

    .btn-quest-action:hover {
        background: #B45309;
        color: #FFFFFF;
    }

    /* 3. COMPACT BADGES GRID */
    .compact-badges-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .compact-badge-item {
        background: #FDFBF8;
        border: 1px solid #EFE6DC;
        border-radius: 14px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .compact-badge-item.unlocked {
        border-color: rgba(212, 117, 44, 0.3);
        background: #FFFDF9;
    }

    .compact-badge-item.locked {
        opacity: 0.7;
    }

    .badge-mini-icon {
        font-size: 24px;
        line-height: 1;
        flex-shrink: 0;
    }

    .badge-mini-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .badge-mini-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-dark);
    }

    .badge-mini-desc {
        font-size: 11.5px;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.35;
    }

    .badge-mini-status {
        font-size: 10.5px;
        font-weight: 800;
        color: #15803D;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-mini-status.locked {
        color: #94A3B8;
    }

    @media (max-width: 900px) {
        .compact-badges-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 600px) {
        .streak-banner-card { padding: 18px; }
        .streak-claim-action-box { align-items: flex-start; width: 100%; }
        .compact-badges-grid { grid-template-columns: 1fr; }
        .quest-list-row { flex-wrap: wrap; }
    }
</style>
@endsection
