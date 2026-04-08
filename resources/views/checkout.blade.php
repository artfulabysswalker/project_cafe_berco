<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Berco Cafe</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
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
                    <li><a href="{{ route('cart') }}">Keranjang <span id="cart-badge" class="badge">0</span></a></li>
                </ul>
            </nav>
            <div class="user-action">
                @auth
                    <span style="color:white; margin-right: 12px;">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-login">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="container checkout-container">
        <div class="checkout-header">
            <h1>Pembayaran</h1>
            <p>Selesaikan pesanan Anda</p>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="checkout-content">
                <div class="checkout-options">
                        <h3>Tipe Layanan</h3>
                        <div class="option-group">
                            <label class="option-item">
                                <input type="radio" name="service_type" value="dine_in" checked>
                                <div class="option-info">
                                    <i class="fas fa-store"></i>
                                    <div>
                                        <strong>Makan di Tempat</strong>
                                        <span>Siap saji langsung</span>
                                    </div>
                                </div>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="service_type" value="take_away">
                                <div class="option-info">
                                    <i class="fas fa-shopping-bag"></i>
                                    <div>
                                        <strong>Bungkus</strong>
                                        <span>Siap untuk dibawa pulang</span>
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
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div>
                                        <strong>Tunai</strong>
                                        <span>Bayar di kasir</span>
                                    </div>
                                </div>
                            </label>
                            <label class="option-item">
                                <input type="radio" name="payment_method" value="transfer">
                                <div class="option-info">
                                    <i class="fas fa-mobile-alt"></i>
                                    <div>
                                        <strong>Transfer Bank</strong>
                                        <span>Via mobile banking</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>
                </div>

                <div class="checkout-summary">
                    <div class="summary-card">
                        <h3>Ringkasan Pesanan</h3>

                        @forelse($cart as $item)
                            <div class="summary-item">
                                <span>{{ $item->menu->nama_menu }} x{{ $item->qty }}</span>
                                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div class="summary-item">
                                <span>Keranjang kosong</span>
                                <span>Rp 0</span>
                            </div>
                        @endforelse
                            <strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>

                        <button type="submit" class="btn-checkout">
                            <i class="fas fa-credit-card"></i>
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>
</body>
</html>