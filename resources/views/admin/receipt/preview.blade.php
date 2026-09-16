@extends('dashboard')

@section('page-title', 'Pratinjau Struk Pesanan #' . $order->id_order)
@section('breadcrumb', 'Preview Struk')

@section('content')
<div class="receipt-preview-page">

    {{-- Top Action Toolbar --}}
    <div class="receipt-toolbar">
        <div>
            <h2 class="receipt-heading"><i class="fas fa-receipt text-amber"></i> Pratinjau Struk Pembayaran</h2>
            <p class="receipt-sub">Pesanan <strong>#{{ $order->id_order }}</strong> • Pelanggan: <strong>{{ $order->nama_pelanggan ?: 'Pelanggan Umum' }}</strong></p>
        </div>
        <div class="receipt-btn-group">
            <a href="{{ route('admin.receipt.print', $order->id_order) }}" target="_blank" class="btn-receipt-action print">
                <i class="fas fa-print"></i> Cetak Struk Thermal
            </a>
            <a href="{{ route('admin.receipt.pdf', $order->id_order) }}" class="btn-receipt-action pdf">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('admin.history') }}" class="btn-receipt-action back">
                <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>
    </div>

    {{-- Main Thermal Paper Container --}}
    <div class="thermal-stage">
        <div class="thermal-paper">
            {{-- Header Logo & Title --}}
            <div class="thermal-header">
                @if(!empty($settings->logo))
                    <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="receipt-logo">
                @endif
                <h3 class="receipt-cafe-name">{{ $settings->cafe_name ?? 'BERCO CAFE' }}</h3>
                <p class="receipt-address">{{ $settings->address ?? 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi' }}</p>
                <p class="receipt-contact">Telp/WA: {{ $settings->phone ?? '+62 821 4103 1234' }}</p>
            </div>

            <div class="receipt-dashed-line"></div>

            {{-- Order Meta Info --}}
            <div class="receipt-meta-grid">
                <div class="meta-row">
                    <span>No. Struk:</span>
                    <strong>#{{ $order->id_order }}</strong>
                </div>
                <div class="meta-row">
                    <span>Waktu:</span>
                    <span>{{ $order->tanggal ? \Carbon\Carbon::parse($order->tanggal)->format('d/m/Y H:i') : ($order->created_at ? $order->created_at->format('d/m/Y H:i') : date('d/m/Y H:i')) }}</span>
                </div>
                <div class="meta-row">
                    <span>Kasir / Staff:</span>
                    <span>{{ $order->user?->name ?? ($order->cashier_name ?? 'Kasir Berco') }}</span>
                </div>
                <div class="meta-row">
                    <span>Pelanggan:</span>
                    <span>{{ $order->nama_pelanggan ?: 'Pelanggan Umum' }}</span>
                </div>
                <div class="meta-row">
                    <span>Metode Bayar:</span>
                    <strong class="payment-tag">{{ strtoupper($order->metode_pembayaran ?? ($order->payment_method ?? 'CASH')) }}</strong>
                </div>
            </div>

            <div class="receipt-dashed-line"></div>

            {{-- Items Table --}}
            <div class="receipt-items-list">
                @if($order->items && $order->items->count() > 0)
                    @foreach($order->items as $item)
                        @php
                            $menuName = $item->menu?->nama_menu ?? ($item->menu?->name ?? 'Menu Produk');
                            $unitPrice = $item->harga_satuan ?? ($item->menu?->harga ?? ($item->subtotal / max(1, $item->quantity)));
                        @endphp
                        <div class="item-entry">
                            <div class="item-top-row">
                                <span class="item-name">{{ $menuName }}</span>
                                <span class="item-subtotal">Rp {{ number_format($item->subtotal ?? ($unitPrice * $item->quantity), 0, ',', '.') }}</span>
                            </div>
                            <div class="item-calc">
                                {{ $item->quantity }} x Rp {{ number_format($unitPrice, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align:center; padding: 8px; color: #94a3b8; font-size: 11px;">Item detail riwayat</div>
                @endif
            </div>

            <div class="receipt-dashed-line"></div>

            {{-- Totals Calculation --}}
            <div class="receipt-totals-table">
                <div class="total-line">
                    <span>Subtotal Menu:</span>
                    <span>Rp {{ number_format($order->subtotal ?? $order->total_harga, 0, ',', '.') }}</span>
                </div>
                @if(!empty($order->discount_amount) && $order->discount_amount > 0)
                    <div class="total-line text-green">
                        <span>Diskon / Potongan:</span>
                        <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if(!empty($order->service_charge) && $order->service_charge > 0)
                    <div class="total-line">
                        <span>Biaya Layanan:</span>
                        <span>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="total-line grand-total">
                    <span>TOTAL BAYAR:</span>
                    <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
                @if(!empty($order->uang_diterima) && $order->uang_diterima > 0)
                    <div class="total-line">
                        <span>Tunai Diterima:</span>
                        <span>Rp {{ number_format($order->uang_diterima, 0, ',', '.') }}</span>
                    </div>
                    <div class="total-line">
                        <span>Kembalian:</span>
                        <span>Rp {{ number_format(max(0, $order->uang_diterima - $order->total_harga), 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <div class="receipt-dashed-line"></div>

            {{-- Footer Notes & WiFi --}}
            <div class="receipt-footer-notes">
                <p class="footer-msg">{{ $settings->footer_message ?? 'Terima kasih atas kunjungan Anda!' }}</p>
                @if(!empty($settings->wifi_name))
                    <div class="wifi-box">
                        <span><i class="fas fa-wifi"></i> WiFi: <strong>{{ $settings->wifi_name }}</strong></span>
                        @if(!empty($settings->wifi_password))
                            <span> | Pass: <strong>{{ $settings->wifi_password }}</strong></span>
                        @endif
                    </div>
                @endif
                <p class="system-tag">*** LUNAS / TERIMA KASIH ***</p>
            </div>
        </div>
    </div>

</div>

<style>
    .receipt-preview-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 100%;
    }

    .receipt-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 18px;
        padding: 20px 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }

    .receipt-heading {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .receipt-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .receipt-btn-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-receipt-action {
        padding: 9px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-receipt-action.print {
        background: linear-gradient(135deg, #D4752C 0%, #B45309 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(212, 117, 44, 0.3);
    }

    .btn-receipt-action.pdf {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
    }

    .btn-receipt-action.back {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-receipt-action:hover {
        transform: translateY(-1px);
    }

    /* Thermal Stage */
    .thermal-stage {
        display: flex;
        justify-content: center;
        padding: 20px 0 60px;
    }

    .thermal-paper {
        width: 380px;
        max-width: 100%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 28px 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        font-family: 'Courier New', Courier, monospace;
        color: #1e293b;
        position: relative;
    }

    .thermal-header {
        text-align: center;
    }

    .receipt-logo {
        max-width: 60px;
        max-height: 60px;
        margin: 0 auto 8px;
        display: block;
    }

    .receipt-cafe-name {
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 1px;
        margin: 0 0 4px 0;
        color: #0f172a;
    }

    .receipt-address, .receipt-contact {
        font-size: 11px;
        margin: 0;
        color: #475569;
        line-height: 1.4;
    }

    .receipt-dashed-line {
        border-top: 1.5px dashed #94a3b8;
        margin: 14px 0;
    }

    .receipt-meta-grid {
        font-size: 11.5px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
    }

    .payment-tag {
        background: #e2e8f0;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 10.5px;
    }

    .receipt-items-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .item-entry {
        display: flex;
        flex-direction: column;
        font-size: 12px;
    }

    .item-top-row {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
    }

    .item-calc {
        font-size: 10.5px;
        color: #64748b;
    }

    .receipt-totals-table {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 11.5px;
    }

    .total-line {
        display: flex;
        justify-content: space-between;
    }

    .total-line.grand-total {
        font-size: 14px;
        font-weight: 900;
        color: #0f172a;
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1px dashed #cbd5e1;
    }

    .text-green {
        color: #16a34a;
    }

    .receipt-footer-notes {
        text-align: center;
        font-size: 11px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .footer-msg {
        margin: 0;
        color: #334155;
        font-style: italic;
    }

    .wifi-box {
        font-size: 10.5px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .system-tag {
        font-size: 10px;
        letter-spacing: 1px;
        color: #94a3b8;
        margin: 4px 0 0 0;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .thermal-paper, .thermal-paper * {
            visibility: visible;
        }
        .thermal-paper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: none;
            padding: 0;
        }
    }
</style>
@endsection
