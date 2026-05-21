@extends('Customerviews.layouts.web')

@section('title', 'Struk Pesanan - Berco Cafe')

@section('content')
<div class="receipt-page">
    <main class="container receipt-container">
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
                    <span class="value" id="order-number">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal & Waktu</span>
                    <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tipe Layanan</span>
                    <span class="value">
                        @if($order->service_type === 'dine_in')
                            <i class="fas fa-store"></i> Dine In
                        @else
                            <i class="fas fa-box"></i> Take Away
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Metode Pembayaran</span>
                    <span class="value">
                        @if($order->payment_method === 'cash')
                            <i class="fas fa-money-bill-wave"></i> Tunai
                        @elseif($order->payment_method === 'debit')
                            <i class="fas fa-credit-card"></i> Kartu Debit
                        @else
                            <i class="fas fa-credit-card"></i> Kartu Kredit
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
                            <span class="item-name">{{ $item->product->name }}</span>
                            <span class="item-qty">x{{ $item->quantity }}</span>
                        </div>
                        <span class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <hr class="receipt-divider">

            <div class="receipt-total">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Pajak (10%)</span>
                    <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
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
        </div>
    </main>
</div>

<style>
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

    .receipt-items {
        font-size: 14px;
    }

    .receipt-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
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
@endsection
