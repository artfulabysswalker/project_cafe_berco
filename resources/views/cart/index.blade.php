<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Berco Cafe</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="menu-page">
    <header class="header">
        <div class="container header-container">
            <div class="logo-area">
                <div class="logo-icon">☕</div>
                <div>
                    <h2 class="logo-text">BERCO</h2>
                    <div class="status-badge"><span>🕒</span> TUTUP</div>
                </div>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('menu') }}">Pesan Menu</a></li>
                    <li>
                        <a href="{{ route('cart') }}" class="active">Keranjang
                            <span class="badge" id="cart-count">{{ $cart->count() }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="user-action">
                @auth
                    <span>Halo, {{ auth()->user()->name }}</span>
                    <a href="#" class="btn-login">Logout</a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="container cart-main-section">
        <div class="cart-items-container">
            @forelse($cart as $item)
                <div class="cart-item">
                    <div class="item-info">
                        <h3>{{ $item->menu->nama_menu }}</h3>
                        <p>Rp {{ number_format($item->menu->harga, 0, ',', '.') }}</p>
                    </div>
                    <div class="item-controls">
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="qty-form">
                            @csrf
                            @method('PATCH')
                            <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                            <input type="number" name="qty" value="{{ $item->qty }}" min="1" class="qty-input">
                            <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="remove-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="remove-btn">Hapus</button>
                        </form>
                    </div>
                    <div class="item-total">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Keranjang Kosong</h3>
                    <p>Belum ada menu yang ditambahkan</p>
                    <a href="{{ route('menu') }}" class="btn-primary">Lihat Menu</a>
                </div>
            @endforelse
        </div>

        @if($cart->count() > 0)
        <div class="cart-summary-card">
            <div class="summary-row">
                <span>Subtotal</span>
                <span class="summary-val">Rp {{ number_format($cart->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            <hr class="summary-divider">
            <div class="summary-row total-row">
                <span>Total</span>
                <span class="total-val">Rp {{ number_format($cart->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout') }}" class="checkout-btn">Checkout</a>
        </div>
        @endif
    </main>

    <script>
        function changeQty(btn, delta) {
            const input = btn.parentElement.querySelector('.qty-input');
            const currentValue = parseInt(input.value);
            const newValue = currentValue + delta;
            if (newValue >= 1) {
                input.value = newValue;
            }
        }
    </script>
</body>
</html>