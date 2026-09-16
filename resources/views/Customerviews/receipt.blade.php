@extends('Customerviews.layouts.web')

@section('title', 'Struk Pesanan - Berco Cafe')

@section('content')
<div class="receipt-page">
    <main class="container receipt-container">
        @if(session('success'))
            <div class="toast-message" role="status">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        <div class="receipt-box">
            <div class="receipt-header">
                <div class="logo-area" style="text-align: center;">
                    <i class="fas fa-coffee" style="font-size: 40px; color: #bf4f08;"></i>
                    <h1 style="margin: 10px 0;">BERCO CAFE</h1>
                    <p style="margin: 0; color: #999; font-size: 12px;">Nikmati setiap tegukan</p>
                </div>
            </div>

            <div class="receipt-success" style="text-align: center; margin: 30px 0;">
                <div class="success-icon" style="width: 80px; height: 80px; background: #27ae60; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 40px;">
                    <i class="fas fa-check"></i>
                </div>
                <h2 style="color: #27ae60; margin: 15px 0;">Pesanan Berhasil!</h2>
                <p style="color: #666; margin: 0;">Terima kasih telah berbelanja di Berco Cafe</p>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-info">
                <div class="info-row">
                    <span class="label">Nomor Pesanan</span>
                    <span class="value">#{{ $order->id_order }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Nama Pelanggan</span>
                    <span class="value">{{ $order->nama_pelanggan }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal & Waktu</span>
                    <span class="value">{{ $order->tanggal->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status Pesanan</span>
                    <span class="value">
                        <span class="badge" style="background: #3498db; color: white; padding: 4px 8px; border-radius: 3px;">{{ ucfirst($order->status_order) }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Status Pembayaran</span>
                    <span class="value">
                        <span class="badge {{ $order->status_pembayaran === 'paid' ? 'badge-paid' : 'badge-pending' }}">{{ ucfirst($order->status_pembayaran) }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Metode Pembayaran</span>
                    <span class="value">
                        @if($order->payment_method === 'cash')
                            <i class="fas fa-money-bill-wave"></i> Tunai
                        @elseif($order->payment_method === 'card')
                            <i class="fas fa-credit-card"></i> Kartu Debit/Kredit
                        @elseif($order->payment_method === 'e_wallet')
                            <i class="fas fa-wallet"></i> E-Wallet (DANA/OVO/LinkAja)
                        @elseif($order->payment_method === 'bank_transfer')
                            <i class="fas fa-university"></i> Transfer Bank
                        @else
                            <i class="fas fa-credit-card"></i> {{ ucfirst($order->payment_method) }}
                        @endif
                    </span>
                </div>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-items">
                <h3 style="margin: 0 0 15px 0;">Detail Pesanan</h3>
                @foreach($order->items as $item)
                    <div class="receipt-item">
                        <div class="item-name-qty">
                            <span class="item-name">{{ $item->menu->name }}</span>
                            <span class="item-qty">x{{ $item->quantity }}</span>
                        </div>
                        <span class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <hr class="receipt-divider">

            <div class="receipt-total">
                <div class="total-row">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($order->notes)
                <div class="receipt-notes">
                    <p><strong>Catatan:</strong> {{ $order->notes }}</p>
                </div>
            @endif

            <div class="receipt-footer" style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #ddd;">
                <p style="color: #999; font-size: 12px; margin: 0;">Terima kasih atas kunjungan Anda</p>
                <p style="color: #999; font-size: 11px; margin: 5px 0 0 0;">Berco Cafe © 2025</p>
            </div>
        </div>

        @php
            $paymentStatus = strtolower(trim((string) $order->status_pembayaran));
            $orderStatus = strtolower(trim((string) $order->status_order));
            $reviewAllowed = in_array($paymentStatus, ['paid', 'sudah', 'lunas'])
                || in_array($orderStatus, ['completed', 'selesai'])
                || ($paymentStatus === 'pending' && $orderStatus !== 'cancelled');
        @endphp

        <div class="receipt-actions">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Cetak Struk
            </button>
            <a href="{{ route('menu.index') }}" class="btn-continue">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
            <a href="{{ route('order.history') }}" class="btn-history">
                <i class="fas fa-history"></i> Lihat Riwayat Pesanan
            </a>
            @if($reviewAllowed)
                <button type="button" onclick="openReviewModal()" class="btn-review-now">
                    <i class="fas fa-star"></i> Beri Review Sekarang
                </button>
            @endif
        </div>

        @if($reviewAllowed)
            <div id="review-modal" class="review-modal hidden">
                <div class="review-modal-backdrop" onclick="closeReviewModal()"></div>
                <div class="review-modal-card">
                    <button type="button" class="review-modal-close" onclick="closeReviewModal()">×</button>
                    <div class="review-modal-header">
                        <i class="fas fa-star"></i>
                        <div>
                            <h2>Bagikan Review Menu</h2>
                            <p>Pilih item yang Anda pesan dan tuliskan pengalaman singkat Anda.</p>
                        </div>
                    </div>
                    <form id="receipt-review-form" method="POST" action="{{ route('reviews.store', $order->items->first()->menu) }}">
                        @csrf
                        <div class="review-modal-field">
                            <label>Pilih Menu</label>
                            <select id="receipt-review-product-select" class="review-select" required>
                                @foreach($order->items as $item)
                                    <option value="{{ route('reviews.store', $item->menu) }}">{{ $item->menu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="review-modal-field">
                            <label>Rating</label>
                            <select name="rating" required class="review-select">
                                <option value="">Pilih rating</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} bintang</option>
                                @endfor
                            </select>
                        </div>
                        <div class="review-modal-field">
                            <label>Ulasan</label>
                            <textarea name="comment" class="review-textarea" rows="4" placeholder="Bagikan ceritamu tentang menu ini..." required></textarea>
                        </div>
                        <div class="review-modal-actions">
                            <button type="button" class="review-modal-secondary" onclick="closeReviewModal()">Nanti</button>
                            <button type="submit" class="review-modal-primary">Kirim Review</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </main>
</div>

<style>
.toast-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #16a34a;
            color: white;
            padding: 14px 24px;
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            z-index: 1050;
            font-weight: 600;
            animation: fade-in 0.35s ease-out;
        }

        .toast-message.hidden {
            opacity: 0;
            transform: translateX(-50%) translateY(-10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        .receipt-page {
        background: #f5f5f5;
        padding: 40px 20px;
        min-height: calc(100vh - 100px);
    }

    .receipt-container {
        max-width: 500px;
        margin: 0 auto;
    }

    .receipt-box {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .receipt-divider {
        border: none;
        border-top: 1px dashed #ddd;
        margin: 20px 0;
    }

    .receipt-info {
        font-size: 14px;
        line-height: 1.8;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .label {
        color: #666;
        font-weight: bold;
    }

    .value {
        color: #333;
        text-align: right;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 3px;
        color: white;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-paid {
        background: #27ae60;
    }

    .badge-pending {
        background: #e74c3c;
    }

    .item-name-qty {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .item-name {
        color: #333;
    }

    .item-qty {
        color: #999;
        font-size: 12px;
    }

    .item-price {
        color: #bf4f08;
        font-weight: bold;
    }

    .receipt-total {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 4px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .total-row span {
        color: #666;
    }

    .btn-review-now {
        background: #f39c12;
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 12px;
        margin-top: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .btn-review-now:hover {
        background: #d68910;
        transform: translateY(-1px);
    }

    .review-modal.hidden {
        display: none;
    }

    .review-modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .review-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .review-modal-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 540px;
        background: white;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.16);
    }

    .review-modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
        background: transparent;
        border: none;
        font-size: 28px;
        color: #7a5b42;
        cursor: pointer;
        line-height: 1;
    }

    .review-modal-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }

    .review-modal-header i {
        color: #f6b93b;
        font-size: 1.6rem;
    }

    .review-modal-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #6b4b2c;
    }

    .review-modal-header p {
        margin: 6px 0 0;
        color: #7a5b42;
        line-height: 1.6;
    }

    .review-modal-field {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 18px;
    }

    .review-modal-field label {
        color: #5b4632;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .review-modal .review-select,
    .review-modal .review-textarea {
        width: 100%;
        border: 1px solid #d7c0a7;
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 0.95rem;
        color: #4a4036;
        background: #fff;
    }

    .review-modal .review-select:focus,
    .review-modal .review-textarea:focus {
        outline: none;
        border-color: #cd7e26;
        box-shadow: 0 0 0 4px rgba(205, 126, 38, 0.12);
    }

    .review-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .review-modal-secondary,
    .review-modal-primary {
        border: none;
        border-radius: 14px;
        padding: 12px 18px;
        cursor: pointer;
        font-weight: 700;
        min-width: 130px;
    }

    .review-modal-secondary {
        background: #f3f3f3;
        color: #5b4632;
    }

    .review-modal-secondary:hover {
        background: #e0dfdc;
    }

    .review-modal-primary {
        background: #bf4f08;
        color: white;
    }

    .review-modal-primary:hover {
        background: #a63f06;
    }

    .grand-total {
        font-size: 16px !important;
        font-weight: bold !important;
        color: #333 !important;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #ddd;
    }

    .grand-total span:last-child {
        color: #bf4f08 !important;
    }

    .receipt-notes {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 10px 15px;
        margin-top: 15px;
        border-radius: 4px;
        font-size: 13px;
    }

    .receipt-notes p {
        margin: 0;
        color: #856404;
    }

    .receipt-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-print, .btn-continue, .btn-history {
        flex: 1;
        min-width: 150px;
        padding: 12px;
        border-radius: 4px;
        text-align: center;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
        cursor: pointer;
        border: none;
    }

    .btn-print {
        background: #3498db;
        color: white;
    }

    .btn-print:hover {
        background: #2980b9;
    }

    .btn-continue {
        background: #bf4f08;
        color: white;
    }

    .btn-continue:hover {
        background: #a23f06;
    }

    .btn-history {
        background: #95a5a6;
        color: white;
    }

    .btn-history:hover {
        background: #7f8c8d;
    }

    @media print {
        body {
            background: white;
        }

        .receipt-page {
            background: white;
            padding: 0;
        }

        .receipt-actions {
            display: none;
        }

        .receipt-box {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }

    @media (max-width: 600px) {
        .receipt-box {
            padding: 20px;
        }

        .receipt-actions {
            flex-direction: column;
        }

        .btn-print, .btn-continue, .btn-history {
            min-width: auto;
        }
    }
</style>

<script>
    function openReviewModal() {
        const modal = document.getElementById('review-modal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeReviewModal() {
        const modal = document.getElementById('review-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const receiptReviewSelector = document.getElementById('receipt-review-product-select');
        const receiptReviewForm = document.getElementById('receipt-review-form');

        if (receiptReviewSelector && receiptReviewForm) {
            receiptReviewSelector.addEventListener('change', function () {
                receiptReviewForm.action = this.value;
            });
        }

        const toast = document.querySelector('.toast-message');
        if (toast) {
            setTimeout(() => toast.classList.add('hidden'), 3600);
        }
    });
</script>
@endsection
