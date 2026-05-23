@extends('layouts.app')

@section('title', 'Keranjang Belanja - Berco Cafe')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --berco-brown:#6B3F1F;
        --berco-amber:#D97706;
        --berco-cream:#FDF6ED;
        --berco-warm:#F5E6D3;
        --berco-dark:#1C1209;
        --berco-muted:#8B6B4A;
        --berco-gold:#B45309;
        --berco-surface:#FFFBF5;
        --berco-border:rgba(107,63,31,0.12);
        --berco-border-strong:rgba(107,63,31,0.22);
        --berco-shadow:0 18px 50px rgba(28,18,9,0.08);
        --berco-light-shadow:0 8px 24px rgba(28,18,9,0.06);
        --berco-success:#10B981;
        --berco-danger:#DC2626;
        --berco-warning:#F59E0B;
    }
    body {
        font-family:'DM Sans',sans-serif;
        background:radial-gradient(circle at top left, rgba(255,255,255,0.95), rgba(253,246,237,0.95) 38%, rgba(246,223,198,0.9) 80%);
        color:var(--berco-dark);
    }
    .cart-hero {
        background:linear-gradient(135deg, #6B3F1F 0%, #8B5E34 50%, #A0683A 100%);
        padding:4rem 0 2.5rem;
        position:relative;
        overflow:hidden;
    }
    .cart-hero::before {
        content:'';
        position:absolute;
        top:15%;
        right:-10%;
        width:300px;
        height:300px;
        border-radius:50%;
        background:rgba(255,255,255,0.12);
        filter:blur(32px);
        pointer-events:none;
    }
    .cart-hero::after {
        content:'';
        position:absolute;
        bottom:-5%;
        left:5%;
        width:200px;
        height:200px;
        border-radius:50%;
        background:rgba(255,255,255,0.08);
        filter:blur(28px);
        pointer-events:none;
    }
    .cart-hero-inner {
        max-width:1100px;
        margin:0 auto;
        padding:0 2rem;
        position:relative;
        z-index:1;
    }
    .cart-hero-label {
        font-size:12px;
        font-weight:700;
        color:rgba(255,255,255,0.75);
        margin-bottom:0.8rem;
        letter-spacing:0.2em;
        text-transform:uppercase;
    }
    .cart-hero h1 {
        font-family:'Playfair Display',serif;
        font-size:clamp(2.4rem,5vw,3.4rem);
        color:#fff;
        margin:0 0 0.6rem;
        line-height:1.08;
        font-weight:700;
    }
    .cart-hero-sub {
        color:rgba(255,255,255,0.82);
        font-weight:400;
        max-width:640px;
        line-height:1.7;
        font-size:1.05rem;
    }
    .cart-layout {
        max-width:1100px;
        margin:-2rem auto 5rem;
        padding:2.5rem 2rem;
        display:grid;
        grid-template-columns:1fr 380px;
        gap:2rem;
        background:rgba(255,255,255,0.75);
        border-radius:28px;
        box-shadow:0 24px 64px rgba(28,18,9,0.12);
        backdrop-filter:blur(10px);
        border:1px solid rgba(255,255,255,0.7);
        position:relative;
        z-index:2;
    }
    @media(max-width:980px) {
        .cart-layout {
            grid-template-columns:1fr;
            padding:2rem 1.5rem 4rem;
            margin:0 1rem;
        }
        .cart-item {
            grid-template-columns:76px 1fr 80px;
            gap:1rem;
            padding:1.25rem 1.2rem;
        }
        .cart-item-img {
            width:76px;
            height:76px;
        }
    }
    @media(max-width:640px) {
        .cart-hero {
            padding:3rem 0 2rem;
        }
        .cart-hero h1 {
            font-size:1.8rem;
        }
        .cart-layout {
            padding:1.5rem 1rem 3rem;
            gap:1.5rem;
        }
        .cart-item {
            grid-template-columns:64px 1fr 70px;
            gap:0.75rem;
            padding:1rem 1rem;
        }
        .cart-item-img {
            width:64px;
            height:64px;
            border-radius:12px;
        }
        .cart-item-controls {
            min-width:70px;
        }
    }
    .card {
        background:var(--berco-surface);
        border:1px solid var(--berco-border);
        border-radius:20px;
        box-shadow:var(--berco-light-shadow);
        overflow:hidden;
        transition:all 0.3s ease;
    }
    .card:hover {
        box-shadow:0 12px 32px rgba(28,18,9,0.1);
        border-color:rgba(107,63,31,0.2);
    }
    .card-header {
        padding:1.4rem 1.6rem;
        border-bottom:1px solid var(--berco-border);
        display:flex;
        justify-content:space-between;
        align-items:center;
        background:linear-gradient(to right, rgba(107,63,31,0.02), transparent);
    }
    .cart-item {
        display:grid;
        grid-template-columns:88px 1fr 100px;
        gap:1.25rem;
        align-items:flex-start;
        padding:1.5rem 1.6rem;
        border-bottom:1px solid var(--berco-border);
        transition:background 0.2s ease;
    }
    .cart-item:hover {
        background:linear-gradient(to right, rgba(215,119,6,0.04), transparent);
    }
    .cart-item:last-child {
        border-bottom:none;
    }
    .cart-item-img {
        width:88px;
        height:88px;
        border-radius:18px;
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        background:var(--berco-warm);
        border:1px solid var(--berco-border);
    }
    .cart-item-img img {
        width:100%;
        height:100%;
        object-fit:cover;
    }
    .cart-item-header {
        display:flex;
        flex-direction:column;
        gap:0.3rem;
    }
    .cart-item-name {
        font-family:'Playfair Display',serif;
        font-weight:700;
        font-size:1rem;
        color:var(--berco-dark);
    }
    .cart-item-meta {
        color:var(--berco-muted);
        font-size:.84rem;
        line-height:1.5;
    }
    .cart-item-notes {
        margin-top:0.75rem;
        padding:0.85rem 1rem;
        border-radius:12px;
        background:linear-gradient(135deg, rgba(245,230,211,0.6) 0%, rgba(215,119,6,0.04) 100%);
        font-size:0.85rem;
        color:var(--berco-dark);
        max-width:320px;
        border-left:3px solid var(--berco-warning);
        line-height:1.5;
    }
    .cart-item-controls {
        display:flex;
        flex-direction:column;
        gap:0.6rem;
        align-items:flex-end;
        min-width:100px;
    }
    .cart-item-subtotal {
        font-weight:700;
        color:var(--berco-gold);
        font-size:1.05rem;
        letter-spacing:0.01em;
    }
    .qty-control {
        display:flex;
        align-items:center;
        gap:0.5rem;
        justify-content:center;
        background:linear-gradient(135deg, #fff 0%, rgba(253,246,237,0.5) 100%);
        border:1.5px solid var(--berco-border-strong);
        border-radius:12px;
        padding:0.3rem;
        transition:all 0.3s ease;
    }
    .qty-control:hover {
        box-shadow:0 4px 12px rgba(107,63,31,0.1);
        border-color:var(--berco-gold);
    }
    .qty-btn {
        width:34px;
        height:34px;
        border:none;
        border-radius:8px;
        background:transparent;
        color:var(--berco-dark);
        font-size:1rem;
        font-weight:700;
        cursor:pointer;
        transition:all 0.2s ease;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .qty-btn:hover {
        background:var(--berco-cream);
        color:var(--berco-gold);
        transform:scale(1.05);
    }
    .qty-display {
        min-width:32px;
        text-align:center;
        font-weight:700;
        font-size:0.95rem;
        color:var(--berco-dark);
    }
    .btn-remove,
    .add-more-btn,
    .btn-explore,
    .btn-checkout,
    .btn-promo {
        border:none;
        cursor:pointer;
        transition:all 0.3s ease;
        font-weight:600;
    }
    .btn-remove {
        background:transparent;
        color:var(--berco-danger);
        padding:0.4rem 0.8rem;
        font-weight:600;
        font-size:0.85rem;
        cursor:pointer;
        border-radius:8px;
        transition:all 0.2s ease;
    }
    .btn-remove:hover {
        background:rgba(220,38,38,0.08);
        color:#B01030;
    }
    .add-more-btn,
    .btn-explore {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:0.6rem;
        padding:0.95rem 1.5rem;
        border-radius:14px;
        background:linear-gradient(135deg, #fff 0%, #FFFBF5 100%);
        border:1.5px solid var(--berco-border-strong);
        color:var(--berco-dark);
        font-weight:700;
        box-shadow:var(--berco-light-shadow);
        text-decoration:none;
        transition:all 0.3s ease;
        font-size:0.95rem;
    }
    .add-more-btn:hover,
    .btn-explore:hover {
        transform:translateY(-3px);
        box-shadow:0 16px 40px rgba(28,18,9,0.12);
        border-color:var(--berco-brown);
        background:linear-gradient(135deg, #FFFBF5 0%, #fff 100%);
    }
    .btn-checkout {
        display:block;
        width:100%;
        padding:1.15rem 1.25rem;
        border-radius:16px;
        background:linear-gradient(135deg, var(--berco-brown) 0%, #7D4C2B 100%);
        color:#fff;
        font-weight:700;
        text-align:center;
        box-shadow:0 12px 30px rgba(107,63,31,0.2);
        text-decoration:none;
        border:1px solid rgba(255,255,255,0.1);
        font-size:1rem;
        transition:all 0.3s ease;
    }
    .btn-checkout:hover {
        transform:translateY(-2px);
        box-shadow:0 16px 40px rgba(107,63,31,0.28);
        background:linear-gradient(135deg, #7D4C2B 0%, #6B3F1F 100%);
    }
    .btn-promo {
        padding:0.95rem 1.15rem;
        border-radius:12px;
        background:linear-gradient(135deg, var(--berco-amber) 0%, var(--berco-gold) 100%);
        color:#fff;
        font-weight:700;
        box-shadow:0 8px 20px rgba(217,119,6,0.15);
        transition:all 0.3s ease;
    }
    .btn-promo:hover {
        transform:translateY(-2px);
        box-shadow:0 12px 28px rgba(217,119,6,0.25);
    }
    .order-notes {
        padding:1.5rem 1.6rem;
        background:linear-gradient(to right, rgba(107,63,31,0.02), transparent);
    }
    .order-notes label {
        display:block;
        font-weight:700;
        margin-bottom:0.9rem;
        color:var(--berco-dark);
        font-size:1rem;
        letter-spacing:0.02em;
    }
    .order-notes textarea {
        width:100%;
        border:1.5px solid var(--berco-border);
        border-radius:12px;
        min-height:110px;
        padding:1rem;
        font-family:'DM Sans',sans-serif;
        resize:vertical;
        background:#fff;
        color:var(--berco-dark);
        font-size:0.95rem;
        transition:all 0.3s ease;
    }
    .order-notes textarea:focus {
        outline:none;
        border-color:var(--berco-brown);
        box-shadow:0 0 0 3px rgba(107,63,31,0.1);
        background:linear-gradient(135deg, #fff 0%, rgba(253,246,237,0.5) 100%);
    }
    .summary-card {
        position:sticky;
        top:1.5rem;
        align-self:start;
    }
    .summary-section {
        padding:1.4rem 1.6rem;
        border-bottom:1px solid var(--berco-border);
    }
    .summary-section:nth-child(1) {
        background:linear-gradient(to right, rgba(107,63,31,0.02), transparent);
    }
    .summary-section:nth-child(2) {
        background:linear-gradient(135deg, rgba(217,119,6,0.05) 0%, rgba(180,83,9,0.03) 100%);
    }
    .summary-row {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:1rem;
        margin-bottom:0.8rem;
        font-size:0.95rem;
        color:var(--berco-dark);
    }
    .summary-row:last-child {
        margin-bottom:0;
    }
    .summary-section:last-child {
        border-bottom:none;
    }
    .summary-total {
        font-weight:700;
        margin-top:0.8rem;
        padding-top:0.8rem;
        border-top:1px solid var(--berco-border);
        font-size:1.1rem;
    }
    .promo-wrap {
        display:grid;
        grid-template-columns:1fr auto;
        gap:0.75rem;
    }
    .promo-input {
        width:100%;
        border:1.5px solid var(--berco-border);
        border-radius:12px;
        padding:0.95rem 1rem;
        background:#fff;
        color:var(--berco-dark);
        font-size:0.95rem;
        transition:all 0.3s ease;
    }
    .promo-input:focus {
        outline:none;
        border-color:var(--berco-brown);
        box-shadow:0 0 0 3px rgba(107,63,31,0.1);
    }
    .empty-cart {
        text-align:center;
        padding:4rem 2rem;
        background:linear-gradient(135deg, #fff 0%, rgba(253,246,237,0.5) 100%);
        border:1.5px solid var(--berco-border);
        border-radius:20px;
        box-shadow:var(--berco-light-shadow);
    }
    .empty-cart h2 {
        font-family:'Playfair Display',serif;
        margin:0.8rem 0 0.5rem;
        font-size:2rem;
        color:var(--berco-dark);
    }
    .empty-cart p {
        color:var(--berco-muted);
        max-width:36rem;
        margin:0 auto 2rem;
        line-height:1.8;
        font-size:0.95rem;
    }
    .alert-success {
        background:rgba(16,185,129,0.1);
        border:1.5px solid rgba(16,185,129,0.25);
        color:#047857;
        padding:1rem 1.2rem;
        border-radius:12px;
        font-weight:600;
    }
    .cart-item-badge {
        display:inline-flex;
        margin-top:1rem;
        padding:0.6rem 1.1rem;
        border-radius:999px;
        background:rgba(255,255,255,0.15);
        color:#fff;
        font-size:0.85rem;
        letter-spacing:0.04em;
        border:1px solid rgba(255,255,255,0.25);
        font-weight:600;
    }
</style>
@endpush

@section('content')

<div class="cart-hero"><div class="cart-hero-inner"><div class="cart-hero-label">Berco Cafe</div><h1>Keranjang Belanja</h1><div class="cart-hero-sub">Kelola pilihan Anda dan lanjutkan ke pembayaran</div>
    @php $itemCount = $cartItems->sum('quantity') ?? 0; @endphp
    @if($itemCount > 0)
        <div class="cart-item-badge">{{ $itemCount }} item dalam keranjang</div>
    @endif
</div></div>

<div class="cart-layout">

    @if(session('success'))
        <div style="grid-column:1/-1"><div class="alert-success">✓ {{ session('success') }}</div></div>
    @endif

    @if(isset($cartItems) && $cartItems->count() > 0)

    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-header-title">Pesananmu</span>
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none; border:none; color:var(--berco-danger); font-weight:700; cursor:pointer; font-size:0.9rem; transition:all 0.2s ease; padding:0.4rem 0.8rem; border-radius:8px;">
                        Hapus semua
                    </button>
                </form>
            </div>

            @foreach($cartItems as $item)
                <div class="cart-item">
                    <div class="cart-item-img">
                        @if(isset($item->menu->image) && $item->menu->image)
                            <img src="{{ asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}">
                        @else
                            <span style="font-size:1.6rem">☕</span>
                        @endif
                    </div>

                    <div class="cart-item-content">
                        <div class="cart-item-header">
                            <div class="cart-item-name">{{ $item->menu->name ?? 'Item' }}</div>
                            <div class="cart-item-meta">
                                @if(isset($item->menu->category)) 
                                    {{ $item->menu->category }} · 
                                @endif 
                                Rp {{ number_format($item->menu->price ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        @if($item->notes)
                            <div class="cart-item-notes">📝 {{ $item->notes }}</div>
                        @endif
                    </div>

                    <div class="cart-item-controls">
                        <div class="cart-item-subtotal">Rp {{ number_format(($item->menu->price ?? 0) * $item->quantity, 0, ',', '.') }}</div>
                        
                        <div class="qty-control">
                            <button type="button" class="qty-btn" onclick="updateCartQuantity({{ $item->id }}, 'decrease')">−</button>
                            <span class="qty-display">{{ $item->quantity }}</span>
                            <button type="button" class="qty-btn" onclick="updateCartQuantity({{ $item->id }}, 'increase')">+</button>
                        </div>
                        
                        <button type="button" class="btn-remove" onclick="deleteFromCart({{ $item->id }})" style="width:100%; text-align:center;">Hapus</button>
                    </div>
                </div>
            @endforeach

            <div style="padding:1.3rem 1.6rem; border-top:1px solid var(--berco-border); background:linear-gradient(to right, rgba(107,63,31,0.02), transparent);">
                <a href="{{ route('menu.index') }}" class="add-more-btn">
                    <span style="font-size:1.1rem;">+</span> Tambah menu lainnya
                </a>
            </div>
        </div>

        <div class="card" style="margin-top:1.25rem;">
            <div class="order-notes">
                <label for="order-note">Catatan Pesanan (Opsional)</label>
                <textarea id="order-note" name="order_note" rows="3" style="color:var(--berco-dark);">{{ old('order_note', session('order_note')) }}</textarea>
            </div>
        </div>
    </div>

    <div class="summary-card">
        <div class="card">
            <div class="card-header">
                <span class="card-header-title">Ringkasan Pesanan</span>
            </div>
            
            <div class="summary-section">
                @php 
                    $subtotal = $cartItems->sum(fn($i)=>(($i->menu->price ?? 0) * $i->quantity)); 
                    $tax = round($subtotal * 0.11); 
                    $total = $subtotal + $tax; 
                @endphp
                <div class="summary-row">
                    <span>Subtotal ({{ $itemCount }} item)</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>PPN 11%</span>
                    <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Biaya layanan</span>
                    <span style="color:var(--berco-success); font-weight:600;">Gratis</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span style="color:var(--berco-gold); font-size:1.15rem;">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="summary-section">
                <div style="font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--berco-muted); margin-bottom:0.6rem;">
                    Kode Promo
                </div>
                <div class="promo-wrap">
                    <input type="text" name="promo_code" class="promo-input" placeholder="Masukkan kode..." disabled>
                    <button type="button" class="btn-promo" disabled>Pakai</button>
                </div>
                <div style="font-size:0.75rem; color:#999; margin-top:6px;">Fitur promo belum tersedia.</div>
            </div>

            <div class="summary-section">
                <a href="{{ route('checkout') }}" class="btn-checkout">Lanjut ke Pembayaran</a>
            </div>
        </div>

        <div style="padding:1rem 0.5rem; color:var(--berco-muted); font-size:0.85rem; text-align:center;">
            🔒 Transaksi aman & terlindungi
        </div>
    </div>

    @else
        <div class="empty-cart" style="grid-column:1/-1; max-width:500px; margin:0 auto;">
            <div style="font-size:3.5rem; margin-bottom:1rem;">☕</div>
            <h2>Keranjang Anda kosong</h2>
            <p>Jelajahi menu spesial kami dan tambahkan favorit Anda ke keranjang untuk mulai memesan.</p>
            <a href="{{ route('menu.index') }}" class="btn-explore">☕ Jelajahi Menu</a>
        </div>
    @endif
</div>

<script>
function deleteFromCart(itemId){if(confirm('Hapus item ini dari keranjang?')){fetch(`/cart/${itemId}/remove`,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(r=>r.json()).then(d=>{if(d.success){location.reload();}else{alert('Error: '+d.message);}}).catch(e=>{console.error(e);alert('Terjadi kesalahan');});}}
function updateQuantity(itemId,newQuantity){if(newQuantity<1){deleteFromCart(itemId);return;}fetch(`/cart/${itemId}/update`,{method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({quantity:newQuantity})}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert('Error: '+d.message)}).catch(e=>{console.error(e);alert('Terjadi kesalahan')});}
function updateCartQuantity(itemId,action){fetch(`/cart/${itemId}/update`,{method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({action:action})}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert('Error: '+d.message)}).catch(e=>{console.error(e);alert('Terjadi kesalahan')});}
function increaseQuantity(itemId,currentQty){updateQuantity(itemId,currentQty+1);}function decreaseQuantity(itemId,currentQty){if(currentQty>1)updateQuantity(itemId,currentQty-1);}
</script>

@endsection
