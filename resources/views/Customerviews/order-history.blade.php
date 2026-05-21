@extends('Customerviews.layouts.web')

@section('title', 'Riwayat Pesanan - Berco Cafe')

@section('content')
<div class="order-history-page">
    <main class="container order-history-container">
        <div class="history-header">
            <h1>Riwayat Pesanan Saya</h1>
            <p>Lihat semua pesanan Anda</p>
        </div>

        @if($orders->isEmpty())
            <div class="empty-state" style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px;">
                <i class="fas fa-history" style="font-size: 60px; color: #ccc; margin-bottom: 20px; display: block;"></i>
                <h2>Belum ada pesanan</h2>
                <p style="color: #666; margin: 10px 0 20px 0;">Mulai berbelanja dan pesanan Anda akan muncul di sini</p>
                <a href="{{ route('menu.index') }}" class="btn-shop" style="text-decoration: none; display: inline-block; background: #bf4f08; color: white; padding: 10px 20px; border-radius: 4px;">
                    <i class="fas fa-shopping-bag"></i> Mulai Berbelanja
                </a>
            </div>
        @else
            <div class="orders-list">
                @foreach($orders as $order)
                    <div class="order-card">
                        <div class="order-card-header">
                            <div class="order-info">
                                <h3>{{ $order->order_number }}</h3>
                                <p class="order-date">{{ $order->created_at->format('d M Y - H:i') }}</p>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-{{ $order->status }}">
                                    @if($order->status === 'completed')
                                        <i class="fas fa-check-circle"></i> Selesai
                                    @elseif($order->status === 'pending')
                                        <i class="fas fa-clock"></i> Menunggu
                                    @else
                                        <i class="fas fa-times-circle"></i> Dibatalkan
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="detail-row">
                                <span>Tipe Layanan:</span>
                                <span>
                                    @if($order->service_type === 'dine_in')
                                        <i class="fas fa-store"></i> Dine In
                                    @else
                                        <i class="fas fa-box"></i> Take Away
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span>Metode Pembayaran:</span>
                                <span>
                                    @if($order->payment_method === 'cash')
                                        <i class="fas fa-money-bill-wave"></i> Tunai
                                    @elseif($order->payment_method === 'debit')
                                        <i class="fas fa-credit-card"></i> Kartu Debit
                                    @else
                                        <i class="fas fa-credit-card"></i> Kartu Kredit
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span>Jumlah Item:</span>
                                <span><strong>{{ $order->items->count() }} item</strong></span>
                            </div>
                        </div>

                        <div class="order-items">
                            @foreach($order->items as $item)
                                <div class="order-item-row">
                                    <span class="item-name">{{ $item->product->name }} x{{ $item->quantity }}</span>
                                    <span class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-total">
                            <div class="total-row">
                                <span>Total</span>
                                <span class="total-amount">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="order-actions">
                            <a href="{{ route('order.receipt', $order) }}" class="btn-view-receipt receipt-link" data-url="{{ route('order.receipt', $order) }}">
                                <i class="fas fa-receipt"></i> Lihat Struk
                            </a>
                            <a href="{{ route('menu.index') }}" class="btn-order-again">
                                <i class="fas fa-redo"></i> Pesan Lagi
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $orders->links() }}
            </div>
        @endif

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('menu.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    </main>
</div>

<style>
    .order-history-page {
        background: #f5f5f5;
        padding: 40px 20px;
        min-height: calc(100vh - 100px);
    }

    .order-history-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .history-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .history-header h1 {
        font-size: 2em;
        margin: 0 0 10px 0;
        color: #333;
    }

    .history-header p {
        margin: 0;
        color: #666;
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 40px;
    }

    .order-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s;
    }

    .order-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: #f9f9f9;
        border-bottom: 1px solid #eee;
    }

    .order-info h3 {
        margin: 0;
        font-size: 16px;
        color: #333;
    }

    .order-date {
        margin: 3px 0 0 0;
        font-size: 12px;
        color: #999;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-completed {
        background: #d4edda;
        color: #155724;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .order-details {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #666;
    }

    .detail-row span:last-child {
        color: #333;
    }

    .order-items {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background: #fafafa;
    }

    .order-item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .order-item-row:last-child {
        margin-bottom: 0;
    }

    .item-name {
        color: #333;
    }

    .item-price {
        color: #bf4f08;
        font-weight: bold;
    }

    .order-total {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    .total-amount {
        color: #bf4f08;
    }

    .order-actions {
        display: flex;
        gap: 10px;
        padding: 15px 20px;
    }

    .btn-view-receipt, .btn-order-again {
        flex: 1;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        border-radius: 4px;
        font-size: 13px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-view-receipt {
        background: #3498db;
        color: white;
    }

    .btn-view-receipt:hover {
        background: #2980b9;
    }

    .btn-order-again {
        background: #bf4f08;
        color: white;
    }

    .btn-order-again:hover {
        background: #a23f06;
    }

    .btn-back {
        display: inline-block;
        padding: 10px 20px;
        background: #f0f0f0;
        color: #333;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #e0e0e0;
    }

    @media (max-width: 600px) {
        .order-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .order-actions {
            flex-direction: column;
        }
    }
</style>

<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.receipt-link')) {
        e.preventDefault();
        const url = e.target.closest('.receipt-link').dataset.url;
        if (typeof viewReceipt === 'function') {
            viewReceipt(url);
        } else {
            window.location.href = url;
        }
    }
});
</script>
@endsection
