@extends('Customerviews.layouts.web')

@section('title', 'Menu - Berco Cafe')

@section('content')
<div class="menu-page">
    <main class="container menu-section">
        <div class="menu-header">
            <div class="header-info">
                <h1>Menu Berco Cafe</h1>
                <p>Jelajahi menu kami</p>
            </div>
            <div class="menu-header-actions" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                    <i class="fas fa-home"></i>
                    Beranda
                </a>
                @auth
                    <a href="{{ route('redeem.index') }}" class="inline-flex items-center gap-2 rounded-full bg-[#f59e0b] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#d97706]">
                        <i class="fas fa-gift"></i>
                        Tukar EXP
                    </a>
                    <a href="{{ route('daily-quest') }}" class="inline-flex items-center gap-2 rounded-full bg-[#10b981] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0f766e]">
                        <i class="fas fa-trophy"></i>
                        Daily Quest
                    </a>
                @endauth
                <div class="cart-floating" onclick="window.location.href='{{ route('cart.index') }}'" style="position: relative; cursor: pointer;">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="floating-badge" class="badge" style="display: none; top: -5px; right: -5px;">0</span>
                </div>
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
                <div class="menu-card" data-category="menu" data-id="{{ $product->id }}">
                    <div class="card-img">
                        <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?q=80&w=400' }}" 
                             alt="{{ $product->name }}">
                        <div class="image-overlay"></div>
                        @auth
                            <button class="wishlist-btn {{ in_array($product->id, $favoriteIds ?? []) ? 'liked' : '' }}"
                                    onclick="toggleFavorite({{ $product->id }}, this)"
                                    type="button">
                                <i class="far fa-heart"></i>
                            </button>
                        @endauth
                        <div class="card-category">
                            <span class="category-badge">Menu</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3>{{ $product->name }}</h3>
                        <p>{{ $product->description ?? 'Menu pilihan' }}</p>
                        <span class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>

                        @auth
                            <button class="btn-add-cart add-to-cart-btn" 
                                    data-id="{{ $product->id }}" 
                                    data-name="{{ $product->name }}" 
                                    type="button">
                                <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                            </button>
                        @else
                            <a href="{{ route('testlogin') }}" class="btn-add-cart" style="text-decoration: none; display: block; text-align: center;">
                                <i class="fas fa-shopping-cart"></i> Masuk untuk Pesan
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="text-center" style="grid-column: 1/-1; padding: 40px;">
                    <i class="fas fa-coffee text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-xl">Tidak ada menu yang ditemukan</p>
                    <p class="text-gray-400">Coba ubah filter pencarian Anda</p>
                </div>
            @endforelse
        </div>

        <div class="review-highlights">
            <div class="review-highlights-header">
                <div>
                    <h2>Ulasan Pelanggan</h2>
                    <p>Ringkasan review terbaru agar Anda bisa melihat pengalaman orang lain sebelum pesan.</p>
                </div>
                <div class="review-summary-badge">
                    <span>{{ $totalReviews }} total reviews</span>
                </div>
            </div>

            <div class="review-cards">
                @forelse($recentReviews as $review)
                    <div class="review-card">
                        <div class="review-card-top">
                            <div class="review-card-user">
                                {{ $review->user?->name ?? 'Pelanggan' }}
                                <span class="review-card-meta">{{ $review->created_at->locale('id')->diffForHumans() }}</span>
                            </div>
                            <div class="review-card-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : 'empty' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="review-card-product">{{ $review->product->name ?? 'Menu' }}</div>
                        <p>{{ Str::limit($review->comment ?? 'Pelanggan belum meninggalkan komentar.', 140) }}</p>
                    </div>
                @empty
                    <div class="review-empty">Belum ada review terbaru. Silakan pesan dulu dan beri rating setelah mencoba menu favorit Anda.</div>
                @endforelse
            </div>
        </div>

        @auth
            @if($products->count() > 0)
                <div class="review-submit-section">
                    <div class="review-form">
                        <div class="review-form-title">
                            <i class="fas fa-star review-form-icon"></i>
                            <span>Berikan Review & Rating</span>
                        </div>
                        <form id="menu-review-form" method="POST" action="{{ route('reviews.store', $products->first()) }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-sm text-gray-600">Pilih Menu:</label>
                                    <select id="review-product-select" class="review-select" required>
                                        @foreach($products as $product)
                                            <option value="{{ route('reviews.store', $product) }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Rating:</label>
                                    <select name="rating" required class="review-select">
                                        <option value="">Pilih rating</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Ulasan:</label>
                                <textarea name="comment" class="review-textarea" rows="4" placeholder="Ceritakan pengalaman Anda..." required></textarea>
                            </div>
                            <button type="submit" class="review-submit-btn">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Review
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="review-empty">Tidak ada menu untuk direview saat ini.</div>
            @endif
        @else
            <div class="review-submit-section">
                <div class="review-empty">Silakan <a href="{{ route('testlogin') }}">masuk</a> terlebih dahulu untuk memberi rating dan review.</div>
            </div>
        @endauth

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: 40px;">
            {{ $products->links() }}
        </div>
    </main>
</div>

<style>
    .menu-page {
        background: linear-gradient(135deg, #f8efe3 0%, #f4e1cc 55%, #f2d2b0 100%);
        padding: 20px 0;
        min-height: 100vh;
    }

    .menu-section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        background: linear-gradient(135deg, #8b5e34 0%, #c78c4e 100%);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        color: white;
    }

    .header-info h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        background: linear-gradient(45deg, #ffffff, #f0f0f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .header-info p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .cart-floating i {
        font-size: 1.5rem;
        color: white;
    }

    .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        border-radius: 50%;
        padding: 5px 8px;
        font-size: 0.8rem;
        font-weight: bold;
        min-width: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
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
        color: #6b4b2c;
        font-size: 14px;
        margin: 15px 0;
    }

    .reviews-summary {
        background: #fff4e7;
        border: 1px solid #f6d3b3;
        padding: 18px;
        border-radius: 18px;
        margin-bottom: 15px;
    }

    .reviews-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .rating-star {
        font-size: 0.95rem;
        color: #d1d5db;
    }

    .rating-star.filled {
        color: #f6b93b;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .rating-score {
        margin-left: 10px;
        font-weight: 700;
        color: #8b5e34;
    }

    .rating-text {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #7a5839;
        background: rgba(255,255,255,0.82);
        border-radius: 999px;
        padding: 8px 12px;
        border: 1px solid rgba(191,117,18,0.18);
    }

    .reviews-note {
        color: #7a5b42;
        font-size: 0.92rem;
        line-height: 1.55;
        margin: 0;
    }

    .review-form {
        background: linear-gradient(135deg, rgba(241,209,182,0.98), rgba(255,250,244,0.95));
        border: 1px solid #ebc8a4;
        border-radius: 18px;
        padding: 18px;
        margin-top: 20px;
    }

    .review-form-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        color: #6b4b2c;
        font-weight: 700;
    }

    .review-form-icon {
        color: #cd7e26;
        font-size: 1rem;
    }

    .review-select,
    .review-textarea {
        width: 100%;
        background: white;
        border: 1px solid #d7c0a7;
        border-radius: 12px;
        padding: 11px 14px;
        color: #4a4036;
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }

    .review-select:focus,
    .review-textarea:focus {
        outline: none;
        border-color: #cd7e26;
        box-shadow: 0 0 0 4px rgba(205,126,38,0.12);
    }

    .review-textarea {
        min-height: 90px;
        resize: vertical;
    }

    .review-submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #bf4f08 0%, #e79e4c 100%);
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .review-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(191,79,8,0.24);
    }

    .review-submit-btn i {
        margin-right: 6px;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .menu-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .menu-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .card-img {
        position: relative;
        overflow: hidden;
        background: #f0f0f0;
    }

    .card-img img {
        display: block;
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .menu-card:hover .image-overlay {
        opacity: 1;
    }

    .wishlist-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-size: 16px;
        color: #bf4f08;
    }

    .wishlist-btn:hover {
        background: #ff6b6b;
        color: white;
        transform: scale(1.1);
    }

    .card-category {
        position: absolute;
        top: 15px;
        left: 15px;
    }

    .category-badge {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card-body {
        padding: 25px;
    }

    .card-body h3 {
        margin: 0 0 8px 0;
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        line-height: 1.3;
    }

    .card-body p {
        margin: 8px 0 15px 0;
        font-size: 0.95rem;
        color: #718096;
        line-height: 1.5;
    }

    .price {
        display: block;
        font-size: 1.2rem;
        font-weight: 700;
        color: #38a169;
        margin-bottom: 20px;
    }

    .btn-add-cart {
        width: 100%;
        background: linear-gradient(135deg, #bf4f08 0%, #e79e4c 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .btn-add-cart:hover {
        background: linear-gradient(135deg, #a23f06 0%, #d98812 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(191, 79, 8, 0.28);
    }

    .btn-add-cart i {
        margin-right: 8px;
    }

    .review-highlights {
        background: #fff8f0;
        border: 1px solid #f3d0b2;
        border-radius: 24px;
        padding: 28px;
        margin-bottom: 40px;
    }

    .review-highlights-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .review-highlights-header h2 {
        font-size: 1.9rem;
        margin-bottom: 6px;
        color: #7a4520;
    }

    .review-highlights-header p {
        color: #775d4a;
    }

    .review-summary-badge {
        background: white;
        border: 1px solid #f2d3be;
        border-radius: 999px;
        padding: 10px 16px;
        color: #7a4520;
        font-weight: 700;
    }

    .review-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    .review-card {
        background: white;
        border: 1px solid #f2dbcd;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 10px 25px rgba(109, 70, 35, 0.08);
    }

    .review-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .review-card-user {
        font-weight: 700;
        color: #6f4e2c;
        line-height: 1.4;
    }

    .review-card-meta {
        display: inline-block;
        margin-top: 6px;
        font-size: 0.85rem;
        color: #9b7b64;
    }

    .review-card-rating {
        display: flex;
        gap: 4px;
    }

    .review-card-rating .filled {
        color: #f6b93b;
    }

    .review-card-rating .empty {
        color: #dcd7d0;
    }

    .review-card-product {
        font-size: 0.95rem;
        color: #8b5e34;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .review-card p {
        color: #6c4b36;
        line-height: 1.7;
        font-size: 0.95rem;
        margin: 0;
    }

    .review-empty {
        padding: 30px;
        background: #fff4e7;
        border: 1px dashed #f0c9a6;
        border-radius: 18px;
        color: #7a5b42;
        text-align: center;
        font-weight: 600;
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

function toggleFavorite(productId, button) {
    fetch('{{ route('favorites.toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('liked', data.favorited);
        } else {
            alert(data.message || 'Gagal menyimpan favorit');
        }
    })
    .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan saat menyimpan favorit');
    });
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

function attachCartButtons() {
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;

            if (productId && productName) {
                addToCart(productId, productName);
            }
        });
    });
}

// Update badge and attach events on page load
document.addEventListener('DOMContentLoaded', function() {
    attachCartButtons();
    updateCartBadge();

    const reviewSelector = document.getElementById('review-product-select');
    const reviewForm = document.getElementById('menu-review-form');

    if (reviewSelector && reviewForm) {
        reviewSelector.addEventListener('change', function () {
            reviewForm.action = this.value;
        });
    }
});
</script>
@endauth
@endsection
