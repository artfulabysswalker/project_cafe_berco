@extends('layouts.web')

@section('title', 'Menu - Berco Cafe')

@section('content')
<div class="menu-page">
    <main class="container menu-section">
        <div class="menu-header">
            <div class="header-info">
                <h1>Menu Berco Cafe</h1>
                <p>Jelajahi menu kami</p>
            </div>
            <div class="cart-floating" onclick="window.location.href='{{ route('cart.index') }}'" style="position: relative; cursor: pointer;">
                <i class="fas fa-shopping-cart"></i>
                <span id="floating-badge" class="badge" style="display: none; top: -5px; right: -5px;">0</span>
            </div>
        </div>

        <div class="filter-bar">
            <form method="GET" action="{{ route('menu.index') }}" class="filter-form">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari menu..." value="{{ request('search') }}">
                </div>
                <select name="price" onchange="this.form.submit()">
                    <option value="all">Semua Harga</option>
                    <option value="low" {{ request('price') === 'low' ? 'selected' : '' }}>Dibawah Rp 15.000</option>
                    <option value="high" {{ request('price') === 'high' ? 'selected' : '' }}>Rp 15.000 ke Atas</option>
                </select>
            </form>
        </div>

        <div class="category-tabs">
            <a href="{{ route('menu.index') }}" class="category-btn {{ !request('category') || request('category') === 'all' ? 'active' : '' }}">Semua</a>
            @foreach(['kopi' => 'Kopi', 'non-kopi' => 'Non Kopi', 'ice-blend' => 'Ice Blended', 'snack' => 'Snack', 'dessert' => 'Dessert', 'makanan' => 'Makanan'] as $key => $label)
                <a href="{{ route('menu.index', ['category' => $key]) }}" class="category-btn {{ request('category') === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        <p class="menu-count">Menampilkan {{ $products->total() }} menu</p>

        <div class="menu-grid">
            @forelse($products as $product)
                <div class="menu-card" data-category="{{ $product->category }}" data-id="{{ $product->slug }}">
                    <div class="card-img">
                        <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?q=80&w=400' }}" 
                             alt="{{ $product->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @auth
                            <button class="wishlist-btn" onclick="toggleFavorite('{{ $product->slug }}', this)" type="button">
                                <i class="far fa-heart"></i>
                            </button>
                        @endauth
                    </div>
                    <div class="card-body">
                        <h3>{{ $product->name }}</h3>
                        <p>{{ $product->description ?? 'Menu pilihan' }}</p>
                        <span class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @auth
                            <button class="btn-add-cart" onclick="addToCart({{ $product->id }}, '{{ $product->name }}')" type="button">
                                <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn-add-cart" style="text-decoration: none; display: block; text-align: center;">
                                <i class="fas fa-shopping-cart"></i> Masuk untuk Pesan
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="text-center" style="grid-column: 1/-1; padding: 40px;">
                    <p>Tidak ada menu yang ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: 40px;">
            {{ $products->links() }}
        </div>
    </main>
</div>

<style>
    .menu-page {
        background: #f5f5f5;
        padding: 20px 0;
    }

    .menu-section {
        max-width: 1200px;
        margin: 0 auto;
    }

    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        background: white;
        padding: 20px;
        border-radius: 8px;
    }

    .header-info h1 {
        font-size: 2em;
        margin: 0;
        color: #333;
    }

    .header-info p {
        margin: 5px 0 0 0;
        color: #666;
    }

    .cart-floating {
        width: 50px;
        height: 50px;
        background: #bf4f08;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: background 0.3s;
    }

    .cart-floating:hover {
        background: #a23f06;
    }

    .filter-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
    }

    .search-input input {
        border: none;
        outline: none;
        flex: 1;
        margin-left: 8px;
        font-size: 14px;
    }

    .filter-bar select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
    }

    .category-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        overflow-x: auto;
        flex-wrap: wrap;
    }

    .category-btn {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 20px;
        background: white;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
    }

    .category-btn.active {
        background: #bf4f08;
        color: white;
        border-color: #bf4f08;
    }

    .category-btn:hover {
        border-color: #bf4f08;
        color: #bf4f08;
    }

    .menu-count {
        color: #666;
        font-size: 14px;
        margin: 15px 0;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .menu-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card-img {
        position: relative;
        overflow: hidden;
        background: #f0f0f0;
    }

    .card-img img {
        display: block;
    }

    .wishlist-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #bf4f08;
        transition: all 0.3s;
    }

    .wishlist-btn:hover {
        background: #bf4f08;
        color: white;
    }

    .card-body {
        padding: 15px;
    }

    .card-body h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        color: #333;
    }

    .card-body p {
        margin: 5px 0;
        font-size: 13px;
        color: #666;
    }

    .price {
        display: block;
        font-size: 16px;
        font-weight: bold;
        color: #bf4f08;
        margin: 10px 0;
    }

    .btn-add-cart {
        width: 100%;
        padding: 10px;
        background: #bf4f08;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }

    .btn-add-cart:hover {
        background: #a23f06;
    }

    .badge {
        background: #ff4444;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
</style>

@auth
<script>
function addToCart(productId, productName) {
    fetch('{{ route('cart.add') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + productName + ' ditambahkan ke keranjang!');
            updateCartBadge();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function toggleFavorite(productSlug, button) {
    // You can implement favorite functionality later
    button.classList.toggle('liked');
}

function updateCartBadge() {
    fetch('{{ route('cart.count') }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('floating-badge');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
}

// Update badge on page load
document.addEventListener('DOMContentLoaded', updateCartBadge);
</script>
@endauth
@endsection
