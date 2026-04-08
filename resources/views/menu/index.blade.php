<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Berco Cafe</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="menu-page">

    <header class="header">
        <div class="container header-container">
            <div class="logo-area">
                <i class="fas fa-coffee cup-icon"></i>
                <div class="logo-text">
                    <h1 class="brand-name">BERCO</h1>
                    <span class="status-badge"><i class="far fa-clock"></i> TUTUP</span>
                </div>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="{{ route('menu') }}" class="active"><i class="fas fa-mug-hot"></i> Pesan Menu</a></li>
                    <li>
                        <a href="{{ route('cart') }}" style="position: relative;">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                            <span id="cart-badge" class="badge" style="display: inline-block;">{{ $cartCount }}</span>
                        </a>
                    </li>
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
                    <a href="{{ route('login') }}" class="btn-login">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="container menu-section">
        <div class="menu-header">
            <div class="header-info">
                <h1>Menu Berco Cafe</h1>
                <p>Jelajahi menu kami</p>
            </div>
            <div class="cart-floating" onclick="window.location.href='{{ route('cart') }}'" style="position: relative; cursor: pointer;">
                <i class="fas fa-shopping-cart"></i>
                <span id="floating-badge" class="badge" style="display: inline-block; top: -5px; right: -5px;">{{ $cartCount }}</span>
            </div>
        </div>

        <form method="GET" class="filter-bar">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" name="search" placeholder="Cari menu..." value="{{ request('search') }}">
            </div>
            <select id="price-filter" name="price_filter">
                <option value="all" {{ request('price_filter') === 'all' ? 'selected' : '' }}>Semua Harga</option>
                <option value="low" {{ request('price_filter') === 'low' ? 'selected' : '' }}>Dibawah Rp 15.000</option>
                <option value="high" {{ request('price_filter') === 'high' ? 'selected' : '' }}>Rp 15.000 ke Atas</option>
            </select>
            <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
        </form>

        <div class="category-tabs">
            <button class="active" type="button">Semua</button>
            <button type="button">Kopi</button>
            <button type="button">Non Kopi</button>
            <button type="button">Ice Blended</button>
            <button type="button">Snack</button>
            <button type="button">Dessert</button>
            <button type="button">Makanan</button>
        </div>

        <p class="menu-count">Menampilkan {{ $menus->total() }} menu</p>

        <div class="menu-grid">
            @forelse($menus as $menu)
                <div class="menu-card" data-category="kopi" data-id="menu-{{ $menu->id_menu }}">
                    <div class="card-img">
                        <img src="{{ $menuImages[$menu->nama_menu] ?? 'https://via.placeholder.com/400x180?text=Menu' }}" alt="{{ $menu->nama_menu }}">
                        <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="card-body">
                        <h3>{{ $menu->nama_menu }}</h3>
                        <p>{{ $menu->status_tersedia ? 'Menu tersedia' : 'Menu tidak tersedia' }}</p>
                        <span class="price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                        @if($menu->status_tersedia)
                            @auth
                                <form action="{{ route('cart.add', $menu->id_menu) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-add-cart">
                                        <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn-add-cart">
                                    <i class="fas fa-sign-in-alt"></i> Login untuk Tambah
                                </a>
                            @endauth
                        @else
                            <button type="button" class="btn-add-cart" disabled style="background: #999; cursor: not-allowed;">
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="menu-card" style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                    Belum ada menu tersedia.
                </div>
            @endforelse
        </div>

        <div style="margin-bottom: 60px;">
            {{ $menus->withQueryString()->links() }}
        </div>
    </main>
</body>
</html>