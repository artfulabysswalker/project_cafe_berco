@extends('Customerviews.layouts.web')

@section('title', 'Pembayaran - Berco Cafe')

@section('content')
<div class="checkout-page">
    <main class="container checkout-container">
        <div class="checkout-header">
            <h1>Pembayaran</h1>
            <p>Selesaikan pesanan Anda</p>
        </div>

        <div class="checkout-content">
            <div class="checkout-options">
                
                <section class="checkout-card">
                    <h3>Tipe Layanan</h3>
                    <div class="option-group">
                        <label class="option-item">
                            <input type="radio" name="service_type" value="dine_in" checked>
                            <div class="option-info">
                                <i class="fas fa-store"></i>
                                <div>
                                    <strong>Dine In</strong>
                                    <span>Makan di tempat</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="service_type" value="take_away">
                            <div class="option-info">
                                <i class="fas fa-box"></i>
                                <div>
                                    <strong>Take Away</strong>
                                    <span>Bungkus dibawa pulang</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="checkout-card">
                    <h3>Metode Pembayaran</h3>
                    <div class="option-group">
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="cash" checked>
                            <div class="option-info">
                                <i class="fas fa-money-bill-wave" style="color: #27ae60;"></i>
                                <div>
                                    <strong>Tunai</strong>
                                    <span>Bayar dengan uang tunai</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="debit">
                            <div class="option-info">
                                <i class="fas fa-credit-card" style="color: #2980b9;"></i>
                                <div>
                                    <strong>Kartu Debit</strong>
                                    <span>Bayar dengan kartu debit</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="credit">
                            <div class="option-info">
                                <i class="fas fa-credit-card" style="color: #8e44ad;"></i>
                                <div>
                                    <strong>Kartu Kredit</strong>
                                    <span>Bayar dengan kartu kredit</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="checkout-card">
                    <h3>Catatan (Opsional)</h3>
                    <textarea name="notes" placeholder="Contoh: Kurangi gula, tidak ada es, dll..." class="notes-textarea" maxlength="500"></textarea>
                </section>
            </div>

            <div class="checkout-summary">
                <div class="summary-card">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="order-items">
                        @foreach($cartItems as $item)
                            <div class="summary-item">
                                <div class="item-info">
                                    <span class="item-name">{{ $item->product->name }}</span>
                                    <span class="item-qty">x{{ $item->quantity }}</span>
                                </div>
                                <span class="item-total">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="divider">

                    <div class="summary-calculation">
                        <div class="calc-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="calc-row">
                            <span>Pajak (10%)</span>
                            <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="calc-row total">
                            <span>Total</span>
                            <span id="total-amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button class="btn-pay" onclick="processPayment()">
                        <i class="fas fa-check"></i> Konfirmasi Pembayaran
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .checkout-page {
        background: #f5f5f5;
        padding: 20px 0;
        min-height: calc(100vh - 100px);
    }

    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .checkout-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .checkout-header h1 {
        margin: 0 0 10px 0;
        font-size: 2em;
        color: #333;
    }

    .checkout-header p {
        margin: 0;
        color: #666;
    }

    .checkout-content {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 20px;
        margin-bottom: 40px;
    }

    @media (max-width: 768px) {
        .checkout-content {
            grid-template-columns: 1fr;
        }
    }

    .checkout-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .checkout-card h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #333;
    }

    .option-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .option-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .option-item:hover {
        border-color: #bf4f08;
        background: #fff9f5;
    }

    .option-item input[type="radio"] {
        margin-right: 12px;
        cursor: pointer;
        width: 18px;
        height: 18px;
        accent-color: #bf4f08;
    }

    .option-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .option-info i {
        font-size: 20px;
    }

    .option-info div strong {
        display: block;
        color: #333;
    }

    .option-info div span {
        font-size: 12px;
        color: #999;
    }

    .option-item input[type="radio"]:checked + .option-info div strong {
        color: #bf4f08;
    }

    .notes-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        resize: vertical;
        min-height: 80px;
    }

    .notes-textarea:focus {
        outline: none;
        border-color: #bf4f08;
    }

    .checkout-summary {
        background: white;
        border-radius: 8px;
        padding: 20px;
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .summary-card h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #333;
    }

    .order-items {
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .item-info {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .item-name {
        font-size: 14px;
        color: #333;
    }

    .item-qty {
        font-size: 12px;
        color: #999;
    }

    .item-total {
        font-weight: bold;
        color: #bf4f08;
    }

    .divider {
        border: none;
        border-top: 2px solid #eee;
        margin: 15px 0;
    }

    .summary-calculation {
        margin-bottom: 20px;
    }

    .calc-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
        color: #666;
    }

    .calc-row.total {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #eee;
    }

    .btn-pay {
        width: 100%;
        padding: 12px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        margin-bottom: 10px;
    }

    .btn-pay:hover {
        background: #229954;
    }

    .btn-cancel {
        display: block;
        width: 100%;
        padding: 12px;
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: #e0e0e0;
    }
</style>

<script>
function processPayment() {
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const notes = document.querySelector('textarea[name="notes"]').value;

    const btn = document.querySelector('.btn-pay');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    fetch('{{ route('order.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            service_type: serviceType,
            payment_method: paymentMethod,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Konfirmasi Pembayaran';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Konfirmasi Pembayaran';
    });
}
</script>
@endsection
