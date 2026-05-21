@extends('Customerviews.layouts.web')

@section('title', 'Rewards - Berco Cafe')

@section('content')
<div class="rewards-page">
    <main class="container">
        <section class="rewards-hero card-hero">
            <div class="hero-grid">
                <div>
                    <span class="section-label">Rewards Center</span>
                    <h1 class="section-title">Tukar poinmu dengan reward menarik di Berco.</h1>
                    <p class="section-copy">Dari diskon spesial hingga merchandise eksklusif, semua bisa didapatkan dengan poin loyalty kamu.</p>
                </div>
                <div class="hero-summary card-summary">
                    <span class="summary-label">Poin Kamu</span>
                    <strong class="summary-value">{{ auth()->user()->points ?? 0 }}</strong>
                    <p class="summary-copy">Poin yang bisa ditukar dengan berbagai reward menarik.</p>
                </div>
            </div>
        </section>

        <section class="rewards-grid">
            <article class="reward-card reward-discount">
                <div class="reward-header">
                    <div class="reward-icon">💰</div>
                    <div class="reward-points">500 Poin</div>
                </div>
                <h3>Diskon 20%</h3>
                <p>Diskon 20% untuk semua menu dengan minimal pembelian Rp50.000</p>
                <button class="btn-redeem" onclick="redeemReward('discount-20')">Tukar Sekarang</button>
            </article>

            <article class="reward-card reward-free">
                <div class="reward-header">
                    <div class="reward-icon">☕</div>
                    <div class="reward-points">750 Poin</div>
                </div>
                <h3>Free Coffee</h3>
                <p>Kopi gratis satu porsi untuk semua varian kopi spesial Berco</p>
                <button class="btn-redeem" onclick="redeemReward('free-coffee')">Tukar Sekarang</button>
            </article>

            <article class="reward-card reward-merch">
                <div class="reward-header">
                    <div class="reward-icon">👕</div>
                    <div class="reward-points">1500 Poin</div>
                </div>
                <h3>T-Shirt Berco</h3>
                <p>T-shirt eksklusif Berco dengan desain limited edition</p>
                <button class="btn-redeem" onclick="redeemReward('tshirt')">Tukar Sekarang</button>
            </article>

            <article class="reward-card reward-vip">
                <div class="reward-header">
                    <div class="reward-icon">👑</div>
                    <div class="reward-points">2000 Poin</div>
                </div>
                <h3>VIP Access</h3>
                <p>Akses VIP lounge dan prioritas pelayanan selama sebulan</p>
                <button class="btn-redeem" onclick="redeemReward('vip-access')">Tukar Sekarang</button>
            </article>

            <article class="reward-card reward-birthday">
                <div class="reward-header">
                    <div class="reward-icon">🎂</div>
                    <div class="reward-points">1000 Poin</div>
                </div>
                <h3>Birthday Cake</h3>
                <p>Kue ulang tahun gratis untuk merayakan hari spesialmu</p>
                <button class="btn-redeem" onclick="redeemReward('birthday-cake')">Tukar Sekarang</button>
            </article>

            <article class="reward-card reward-bundle">
                <div class="reward-header">
                    <div class="reward-icon">🎁</div>
                    <div class="reward-points">1200 Poin</div>
                </div>
                <h3>Coffee Bundle</h3>
                <p>Paket 3 kopi spesial dengan berbagai rasa pilihan</p>
                <button class="btn-redeem" onclick="redeemReward('coffee-bundle')">Tukar Sekarang</button>
            </article>
        </section>

        <section class="redemption-history card-list">
            <div class="list-header">
                <div>
                    <h2>Riwayat Penukaran</h2>
                    <p>Lihat reward yang sudah kamu tukarkan sebelumnya.</p>
                </div>
            </div>

            <div class="history-list">
                <div class="history-item">
                    <div class="history-icon">💰</div>
                    <div class="history-content">
                        <h4>Diskon 20%</h4>
                        <p>Ditukarkan pada 15 Januari 2025</p>
                    </div>
                    <div class="history-points">-500 Poin</div>
                </div>
                <div class="history-item">
                    <div class="history-icon">☕</div>
                    <div class="history-content">
                        <h4>Free Coffee</h4>
                        <p>Ditukarkan pada 10 Januari 2025</p>
                    </div>
                    <div class="history-points">-750 Poin</div>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
    .rewards-page {
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
    .rewards-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        margin-bottom: 32px;
    }
    .reward-card {
        border-radius: 24px;
        padding: 28px;
        background: white;
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    .reward-card::before {
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
    .reward-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 32px 80px rgba(0, 0, 0, 0.15);
    }
    .reward-card:hover::before {
        opacity: 1;
    }
    .reward-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .reward-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    .reward-card:hover .reward-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .reward-points {
        background: linear-gradient(135deg, #bf4f08 0%, #d97706 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(191, 79, 8, 0.3);
    }
    .reward-card h3 {
        margin: 0;
        font-size: 1.4rem;
        color: #111827;
    }
    .reward-card p {
        margin: 0;
        color: #4b5563;
        line-height: 1.6;
    }
    .btn-redeem {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 999px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    .btn-redeem::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    .btn-redeem:hover::before {
        left: 100%;
    }
    .btn-redeem:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }
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
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .history-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .history-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }
    .history-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    }
    .history-content h4 {
        margin: 0 0 4px;
        font-size: 1.1rem;
        color: #111827;
    }
    .history-content p {
        margin: 0;
        font-size: 0.9rem;
        color: #6b7280;
    }
    .history-points {
        margin-left: auto;
        font-weight: 700;
        color: #dc2626;
    }
    @media (max-width: 1024px) {
        .hero-grid { grid-template-columns: 1fr; }
        .rewards-grid { grid-template-columns: 1fr; }
    }
</style>

<script>
function redeemReward(rewardType) {
    if (confirm('Apakah kamu yakin ingin menukarkan poin untuk reward ini?')) {
        // Implementasi penukaran reward akan ditambahkan nanti
        alert('Reward berhasil ditukarkan! Kami akan menghubungi kamu untuk pengambilan.');
    }
}
</script>
@endsection