@extends('Customerviews.layouts.web')

@section('title', 'Tukar EXP & Rewards - Berco Cafe')

@section('content')
<div class="redeem-minimal-page">
    <div class="redeem-max-container">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-minimal alert-success-min">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert-minimal alert-danger-min">
                <i class="fas fa-circle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 1. COMPACT USER BALANCE & HEADER BAR --}}
        <div class="redeem-top-card">
            <div class="top-info-left">
                <span class="loyalty-pill"><i class="fas fa-gift text-amber"></i> Loyalty Rewards</span>
                <h1 class="top-title">Tukar EXP Poin Anda</h1>
                <p class="top-subtitle">Tukarkan poin EXP yang kamu kumpulkan dari pesanan & quest dengan voucher diskon, menu gratis, atau merchandise.</p>
            </div>

            <div class="user-balance-box">
                <div class="balance-meta">
                    <span class="balance-lbl">Saldo EXP Anda</span>
                    <strong class="balance-val">{{ number_format(auth()->user()->exp ?? 0) }} <span>EXP</span></strong>
                </div>
                <div class="balance-actions">
                    <a href="{{ route('daily.quest') }}" class="btn-balance-link">
                        <i class="fas fa-calendar-check"></i> Daily Quest
                    </a>
                    <a href="{{ route('redeem.history') }}" class="btn-balance-link secondary">
                        <i class="fas fa-history"></i> Riwayat
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. REWARDS LIST (MINIMALIST LIST LAYOUT) --}}
        <div class="rewards-list-container">
            <div class="list-section-header">
                <div>
                    <h2 class="list-title">Daftar Voucher & Hadiah</h2>
                    <p class="list-desc">Pilih reward sesuai saldo EXP Anda. Klik tombol tukar untuk mendapatkan voucher.</p>
                </div>
                <span class="badge-total-items">{{ $rewards->count() }} Hadiah</span>
            </div>

            <div class="minimal-rewards-list">
                @forelse($rewards as $reward)
                    @php
                        $userExp = auth()->user()->exp ?? 0;
                        $canAfford = $userExp >= $reward->exp_cost;
                        $isAvailable = $reward->available;
                        
                        $name = strtolower($reward->name);
                        $icon = 'fa-gift';
                        if (str_contains($name, 'kopi') || str_contains($name, 'coffee') || str_contains($name, 'latte')) {
                            $icon = 'fa-mug-hot';
                        } elseif (str_contains($name, 'diskon') || str_contains($name, 'voucher') || str_contains($name, 'potongan')) {
                            $icon = 'fa-tag';
                        } elseif (str_contains($name, 'tumbler') || str_contains($name, 'merch') || str_contains($name, 'tshirt')) {
                            $icon = 'fa-box-open';
                        } elseif (str_contains($name, 'vip') || str_contains($name, 'gold')) {
                            $icon = 'fa-crown';
                        }
                    @endphp

                    <div class="reward-list-item {{ $canAfford && $isAvailable ? 'afford' : 'locked' }}">
                        {{-- Left: Icon & Reward Info --}}
                        <div class="reward-item-left">
                            <div class="reward-icon-box">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div class="reward-text-details">
                                <div class="reward-title-row">
                                    <h3 class="reward-item-name">{{ $reward->name }}</h3>
                                    @if(!$isAvailable)
                                        <span class="tag-status out-stock">Habis</span>
                                    @elseif($canAfford)
                                        <span class="tag-status ready">Bisa Ditukar</span>
                                    @endif
                                </div>
                                <p class="reward-item-desc">{{ $reward->description ?? 'Voucher eksklusif persembahan Berco Cafe.' }}</p>
                            </div>
                        </div>

                        {{-- Right: Cost & Action Button --}}
                        <div class="reward-item-right">
                            <div class="reward-cost-pill">
                                <i class="fas fa-star text-amber"></i>
                                <strong>{{ number_format($reward->exp_cost) }}</strong> EXP
                            </div>

                            <div class="reward-action-wrap">
                                @if(!$isAvailable)
                                    <button type="button" class="btn-min-redeem disabled" disabled>
                                        <i class="fas fa-ban"></i> Stok Habis
                                    </button>
                                @elseif(!$canAfford)
                                    <button type="button" class="btn-min-redeem locked" disabled title="Kurang {{ number_format($reward->exp_cost - $userExp) }} EXP lagi">
                                        <i class="fas fa-lock"></i> EXP Kurang
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('redeem.redeem', $reward) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-min-redeem active" onclick="return confirm('Tukarkan {{ number_format($reward->exp_cost) }} EXP untuk {{ $reward->name }}?')">
                                            <i class="fas fa-check"></i> Tukar Sekarang
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-list-state">
                        <i class="fas fa-box-open"></i>
                        <h4>Belum Ada Hadiah Tersedia</h4>
                        <p>Katalog reward sedang disiapkan. Silakan cek kembali beberapa saat lagi!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<style>
    .redeem-minimal-page {
        min-height: 100vh;
        background-color: var(--cream-bg);
        padding: 30px 20px 80px;
    }

    .redeem-max-container {
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

    .alert-danger-min {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #9f1239;
    }

    /* 1. TOP HEADER & EXP BOX */
    .redeem-top-card {
        background: linear-gradient(135deg, #1C1008 0%, #2E190E 100%);
        border: 1px solid rgba(212, 117, 44, 0.25);
        border-radius: 20px;
        padding: 28px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        color: #FFFFFF;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        flex-wrap: wrap;
    }

    .top-info-left {
        flex: 1 1 360px;
    }

    .loyalty-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(212, 117, 44, 0.2);
        border: 1px solid rgba(212, 117, 44, 0.4);
        color: var(--primary-light);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 8px;
    }

    .top-title {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        font-weight: 800;
        color: #FFFFFF;
        margin: 0 0 6px 0;
    }

    .top-subtitle {
        font-size: 13px;
        color: #D6C2B0;
        line-height: 1.5;
        margin: 0;
    }

    .user-balance-box {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-width: 240px;
    }

    .balance-meta {
        display: flex;
        flex-direction: column;
    }

    .balance-lbl {
        font-size: 11px;
        color: #A89280;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .balance-val {
        font-size: 26px;
        font-weight: 800;
        color: #F59E0B;
        line-height: 1.1;
    }

    .balance-val span {
        font-size: 14px;
        color: #FFFFFF;
        font-weight: 600;
    }

    .balance-actions {
        display: flex;
        gap: 8px;
    }

    .btn-balance-link {
        flex: 1;
        background: var(--primary);
        color: #FFFFFF;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: background 0.2s ease;
    }

    .btn-balance-link:hover {
        background: #B45309;
        color: #FFFFFF;
    }

    .btn-balance-link.secondary {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-balance-link.secondary:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* 2. REWARDS LIST CONTAINER */
    .rewards-list-container {
        background: #FFFFFF;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }

    .list-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid rgba(107, 63, 31, 0.08);
        flex-wrap: wrap;
        gap: 10px;
    }

    .list-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0 0 2px 0;
    }

    .list-desc {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 0;
    }

    .badge-total-items {
        background: #FAF4EB;
        border: 1px solid rgba(212, 117, 44, 0.2);
        color: var(--primary-dark);
        font-size: 12px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 999px;
    }

    .minimal-rewards-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Minimalist List Item */
    .reward-list-item {
        background: #FDFBF8;
        border: 1px solid #EFE6DC;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
        flex-wrap: wrap;
    }

    .reward-list-item:hover {
        background: #FFFFFF;
        border-color: rgba(212, 117, 44, 0.4);
        box-shadow: 0 4px 16px rgba(107, 63, 31, 0.06);
        transform: translateX(3px);
    }

    .reward-item-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1 1 340px;
    }

    .reward-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #FAF3EA;
        color: var(--primary);
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(212, 117, 44, 0.15);
    }

    .reward-list-item.afford .reward-icon-box {
        background: #FEF3C7;
        color: #D97706;
    }

    .reward-title-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .reward-item-name {
        font-size: 14.5px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .tag-status {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tag-status.ready {
        background: #DCFCE7;
        color: #15803D;
    }

    .tag-status.out-stock {
        background: #FEE2E2;
        color: #B91C1C;
    }

    .reward-item-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin: 2px 0 0 0;
    }

    .reward-item-right {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
    }

    .reward-cost-pill {
        font-size: 13.5px;
        color: #4A3222;
        background: #FAF4EB;
        border: 1px solid #EADBCE;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .reward-cost-pill strong {
        font-weight: 800;
        color: var(--primary-dark);
        font-size: 15px;
    }

    .btn-min-redeem {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 800;
        font-family: inherit;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-min-redeem.active {
        background: linear-gradient(135deg, var(--primary) 0%, #B45309 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(212, 117, 44, 0.25);
    }

    .btn-min-redeem.active:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(212, 117, 44, 0.35);
    }

    .btn-min-redeem.locked {
        background: #F1F5F9;
        color: #94A3B8;
        cursor: not-allowed;
    }

    .btn-min-redeem.disabled {
        background: #FEE2E2;
        color: #EF4444;
        cursor: not-allowed;
    }

    .empty-list-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .empty-list-state i {
        font-size: 32px;
        color: #CBD5E1;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .redeem-top-card { padding: 20px; }
        .rewards-list-container { padding: 18px; }
        .reward-list-item { flex-direction: column; align-items: flex-start; gap: 12px; }
        .reward-item-right { width: 100%; justify-content: space-between; }
    }
</style>
@endsection
