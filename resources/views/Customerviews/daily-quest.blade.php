@extends('layouts.web')

@section('title', 'Daily Quest - Berco Cafe')

@section('content')
<div class="daily-quest-page">
    <main class="container">
        <section class="quest-hero card-hero">
            <div class="hero-grid">
                <div>
                    <span class="section-label">Daily Quest</span>
                    <h1 class="section-title">Dapatkan badge setiap hari untuk jadi pelanggan setia Berco.</h1>
                    <p class="section-copy">Selesaikan aktivitas pembelian, review, dan menu spesial untuk membuka badge baru serta reward loyalty point.</p>
                </div>
                <div class="hero-summary card-summary">
                    <span class="summary-label">Poin Hari Ini</span>
                    <strong class="summary-value">120</strong>
                    <p class="summary-copy">Total poin yang terkumpul dari pembelian, login, dan review menu.</p>
                </div>
            </div>
        </section>

        <section class="badge-cards">
            <article class="badge-card badge-gold">
                <div class="badge-icon">🏅</div>
                <h2>First Sip</h2>
                <p>Badge untuk pembelian pertama di Berco.</p>
            </article>
            <article class="badge-card badge-fire">
                <div class="badge-icon">🔥</div>
                <h2>Daily Regular</h2>
                <p>Beli menu setiap hari untuk menjaga streak dan naik level.</p>
            </article>
            <article class="badge-card badge-star">
                <div class="badge-icon">⭐</div>
                <h2>Trusted Reviewer</h2>
                <p>Tulis review pada menu yang dipesan untuk mendapatkan badge eksklusif.</p>
            </article>
        </section>

        <section class="quest-list card-list">
            <div class="list-header">
                <div>
                    <h2>Tantangan Hari Ini</h2>
                    <p>Pilih tantangan untuk dipenuhi hari ini dan raih lebih banyak reward.</p>
                </div>
                <a href="{{ route('menu.index') }}" class="cta-link">Buka Menu Sekarang</a>
            </div>

            <div class="quests-grid">
                <article class="quest-box quest-highlight">
                    <div class="quest-meta">
                        <span class="quest-tag">Target</span>
                        <span class="quest-progress">80%</span>
                    </div>
                    <h3>Beli menu senilai Rp100.000</h3>
                    <p>Lengkapi transaksi dengan total belanja besar untuk unlock badge Big Spender.</p>
                    <div class="progress-track"><div class="progress-fill" style="width: 80%;"></div></div>
                </article>
                <article class="quest-box quest-review">
                    <div class="quest-meta">
                        <span class="quest-tag">Review</span>
                        <span class="quest-progress">40%</span>
                    </div>
                    <h3>Review 3 menu favorit</h3>
                    <p>Tulis ulasan singkat untuk 3 menu yang sudah kamu nikmati.</p>
                    <div class="progress-track"><div class="progress-fill fill-blue" style="width: 40%;"></div></div>
                </article>
                <article class="quest-box quest-coffee">
                    <div class="quest-meta">
                        <span class="quest-tag">Kopi</span>
                        <span class="quest-progress">60%</span>
                    </div>
                    <h3>Beli 2 kopi sekaligus</h3>
                    <p>Dapatkan badge Coffee Duo dengan membeli dua varian kopi dalam satu transaksi.</p>
                    <div class="progress-track"><div class="progress-fill fill-green" style="width: 60%;"></div></div>
                </article>
            </div>
        </section>
    </main>
</div>

<style>
    .daily-quest-page {
        padding: 40px 0;
        animation: fadeInUp 0.8s ease-out;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .container {
        max-width: 1100px;
        margin: 0 auto;
    }
    .card-hero {
        border-radius: 30px;
        padding: 36px;
        background: linear-gradient(135deg, #fef3c7 0%, #fee2e2 100%);
        box-shadow: 0 24px 70px rgba(203, 81, 0, 0.1);
        margin-bottom: 32px;
    }
    .hero-grid {
        display: grid;
        gap: 28px;
        grid-template-columns: 1.5fr 1fr;
        align-items: center;
    }
    .section-label {
        display: inline-flex;
        padding: 10px 18px;
        border-radius: 999px;
        background: #fde68a;
        color: #92400e;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .section-title {
        margin: 24px 0 16px;
        font-size: clamp(2rem, 2.5vw, 3rem);
        line-height: 1.05;
        color: #341a0c;
    }
    .section-copy {
        color: #5b4636;
        line-height: 1.8;
        max-width: 640px;
    }
    .card-summary {
        border-radius: 28px;
        background: white;
        padding: 28px;
        box-shadow: 0 18px 40px rgba(126, 45, 0, 0.08);
    }
    .summary-label {
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .summary-value {
        margin: 18px 0 12px;
        font-size: 4rem;
        display: block;
        color: #b45309;
    }
    .summary-copy {
        color: #6b7280;
        line-height: 1.75;
    }
    .badge-cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    .badge-card {
        border-radius: 24px;
        padding: 28px;
        min-height: 230px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 18px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .badge-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .badge-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 32px 80px rgba(0, 0, 0, 0.15);
    }
    .badge-card:hover::before {
        opacity: 1;
    }
    .badge-icon {
        width: 62px;
        height: 62px;
        border-radius: 22px;
        display: grid;
        place-items: center;
        font-size: 1.75rem;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    .badge-card:hover .badge-icon {
        transform: scale(1.1) rotate(5deg);
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .badge-card h2 {
        margin: 0;
        font-size: 1.4rem;
        color: #111827;
    }
    .badge-card p {
        margin: 0;
        font-size: 0.95rem;
        color: #4b5563;
        line-height: 1.75;
    }
    .badge-gold { background: #fff7ed; border: 1px solid #fed7aa; }
    .badge-fire { background: #fffbeb; border: 1px solid #fde68a; }
    .badge-star { background: #eff6ff; border: 1px solid #bfdbfe; }
    .card-list {
        border-radius: 30px;
        padding: 32px;
        background: white;
        box-shadow: 0 24px 70px rgba(112, 84, 52, 0.08);
        border: 1px solid rgba(203, 154, 90, 0.2);
    }
    .list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
        margin-bottom: 28px;
    }
    .list-header h2 {
        margin: 0;
        font-size: 2rem;
        color: #111827;
    }
    .list-header p {
        margin: 0;
        color: #6b7280;
        max-width: 560px;
    }
    .cta-link {
        color: #ffffff;
        background: linear-gradient(135deg, #bf4f08 0%, #d97706 100%);
        padding: 14px 22px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(191, 79, 8, 0.3);
    }
    .cta-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    .cta-link:hover::before {
        left: 100%;
    }
    .cta-link:hover {
        background: linear-gradient(135deg, #9a3412 0%, #b45309 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(191, 79, 8, 0.4);
    }
    .quests-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .quest-box {
        border-radius: 24px;
        padding: 26px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 18px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    .quest-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.6s ease;
    }
    .quest-box:hover::before {
        left: 100%;
    }
    .quest-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        border-color: #d1d5db;
    }
    .quest-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .quest-tag {
        padding: 8px 14px;
        border-radius: 999px;
        background: #fef3c7;
        color: #92400e;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .quest-progress {
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
    }
    .quest-box h3 {
        margin: 0;
        font-size: 1.25rem;
        line-height: 1.4;
    }
    .quest-box p {
        margin: 0;
        color: #4b5563;
        line-height: 1.75;
    }
    .progress-track {
        height: 9px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        margin-top: 10px;
        position: relative;
    }
    .progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #f97316 0%, #ea580c 100%);
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .fill-blue { background: #3b82f6; }
    .fill-green { background: #22c55e; }
    @media (max-width: 1024px) {
        .hero-grid, .badge-cards, .quests-grid { grid-template-columns: 1fr; }
    }
</style>
