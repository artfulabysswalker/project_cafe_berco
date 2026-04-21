@extends('layouts.web')

@section('title', 'Keranjang - Berco Cafe')

@section('content')
<div class="cart-page">
    <main class="container cart-main-section">
        <h1 style="margin-bottom: 30px;">Keranjang Belanja</h1>

        @if($cartItems->isEmpty())
            <div class="empty-cart" style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px;">
                <i class="fas fa-shopping-cart" style="font-size: 60px; color: #ccc; margin-bottom: 20px; display: block;"></i>
                <h2>Keranjang Anda kosong</h2>
                <p style="color: #666; margin: 10px 0 20px 0;">Mulai dengan menjelajahi menu kami</p>
                <a href="{{ route('menu.index') }}" class="btn-add-more" style="text-decoration: none; display: inline-block;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu
                </a>
            </div>
        @else
            <div class="cart-content">
                <div class="cart-items-container">
                    @foreach($cartItems as $item)
                        <div class="cart-item" data-item-id="{{ $item->id }}">
                            <div class="item-image">
                                <img src="{{ $item->product->image_url ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?q=80&w=200' }}" 
                                     alt="{{ $item->product->name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                            </div>
                            <div class="item-details">
                                <h3>{{ $item->product->name }}</h3>
                                <p class="item-price">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="item-quantity">
                                <button type="button" onclick="decreaseQuantity({{ $item->id }}, {{ $item->quantity }})">-</button>
                                <input type="number" class="qty-input" value="{{ $item->quantity }}" 
                                       data-item-id="{{ $item->id }}" 
                                       onchange="updateQuantity({{ $item->id }}, this.value)" min="1" max="100">
                                <button type="button" onclick="increaseQuantity({{ $item->id }}, {{ $item->quantity }})">+</button>
                            </div>
                            <div class="item-subtotal">
                                <p class="subtotal-price">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                            <div class="item-remove">
                                <button type="button" onclick="removeFromCart({{ $item->id }})" class="btn-remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="cart-summary-card">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span class="summary-val">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak (10%)</span>
                        <span class="summary-val">Rp {{ number_format($total * 0.1, 0, ',', '.') }}</span>
                    </div>
                    <hr class="summary-divider">
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span class="total-val" id="total-price">Rp {{ number_format($total * 1.1, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="summary-btns">
                        <a href="{{ route('menu.index') }}" class="btn-add-more">
                            <i class="fas fa-arrow-left"></i> Tambah Item
                        </a>
                        <a href="{{ route('checkout') }}" class="btn-checkout">
                            <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>

<style>
    .cart-page {
        background: #f5f5f5;
        padding: 20px 0;
        min-height: calc(100vh - 100px);
    }

    .cart-main-section {
        max-width: 1200px;
        margin: 0 auto;
    }

    .cart-content {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 20px;
        margin-bottom: 40px;
    }

    @media (max-width: 768px) {
        .cart-content {
            grid-template-columns: 1fr;
        }
    }

    .cart-items-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 100px 1fr 120px 120px 50px;
        gap: 15px;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: background 0.3s;
    }

    .cart-item:hover {
        background: #f9f9f9;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-details h3 {
        margin: 0;
        font-size: 16px;
        color: #333;
    }

    .item-price {
        margin: 5px 0 0 0;
        color: #bf4f08;
        font-weight: bold;
    }

    .item-quantity {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .item-quantity button {
        background: #f0f0f0;
        border: none;
        width: 30px;
        height: 30px;
        cursor: pointer;
        font-size: 14px;
    }

    .item-quantity button:hover {
        background: #e0e0e0;
    }

    .qty-input {
        border: none;
        width: 50px;
        text-align: center;
        font-size: 14px;
    }

    .item-subtotal {
        text-align: right;
    }

    .subtotal-price {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    .btn-remove {
        background: #ff4444;
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-remove:hover {
        background: #dd2222;
    }

    .cart-summary-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .summary-row span {
        color: #666;
    }

    .summary-val {
        font-weight: bold;
        color: #333;
    }

    .summary-divider {
        border: none;
        border-top: 2px solid #eee;
        margin: 12px 0;
    }

    .total-row {
        font-size: 16px !important;
        margin-top: 12px !important;
        margin-bottom: 20px !important;
    }

    .total-row span {
        color: #333;
    }

    .total-val {
        font-size: 18px !important;
        font-weight: bold !important;
        color: #bf4f08 !important;
    }

    .summary-btns {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-add-more, .btn-checkout {
        padding: 12px;
        border-radius: 4px;
        text-align: center;
        font-size: 14px;
        text-decoration: none;
        display: block;
        transition: all 0.3s;
    }

    .btn-add-more {
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-add-more:hover {
        background: #e0e0e0;
    }

    .btn-checkout {
        background: #bf4f08;
        color: white;
    }

    .btn-checkout:hover {
        background: #a23f06;
    }

    @media (max-width: 600px) {
        .cart-item {
            grid-template-columns: 80px 1fr;
            gap: 10px;
        }

        .item-quantity, .item-subtotal, .item-remove {
            grid-column: 2;
        }

        .item-quantity {
            width: 80px;
        }
    }
</style>

<script>
function removeFromCart(itemId) {
    if (confirm('Hapus item ini dari keranjang?')) {
        fetch(`/cart/${itemId}/remove`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}

function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(itemId);
        return;
    }

    fetch(`/cart/${itemId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function increaseQuantity(itemId, currentQty) {
    updateQuantity(itemId, currentQty + 1);
}

function decreaseQuantity(itemId, currentQty) {
    if (currentQty > 1) {
        updateQuantity(itemId, currentQty - 1);
    }
}
</script>
@endsection
