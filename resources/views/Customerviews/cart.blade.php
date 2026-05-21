@extends('layouts.app')

@section('title', 'Keranjang Belanja - Berco Cafe')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
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
    }
    body {
        font-family:'DM Sans',sans-serif;
        background:radial-gradient(circle at top left, rgba(255,255,255,0.95), rgba(253,246,237,0.95) 38%, rgba(246,223,198,0.9) 80%);
        color:var(--berco-dark);
    }
    .cart-hero {
        background:radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 45%),linear-gradient(135deg, #6B3F1F 0%, #8B5E34 100%);
        padding:3.5rem 0 2.25rem;
        position:relative;
        overflow:hidden;
    }
    .cart-hero::before {
        content:'';
        position:absolute;
        top:20%;
        right:-8%;
        width:260px;
        height:260px;
        border-radius:50%;
        background:rgba(255,255,255,0.16);
        filter:blur(24px);
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
        font-size:11px;
        font-weight:700;
        color:rgba(255,255,255,0.72);
        margin-bottom:.65rem;
        letter-spacing:.18em;
        text-transform:uppercase;
    }
    .cart-hero h1 {
        font-family:'Playfair Display',serif;
        font-size:clamp(2.3rem,4vw,3.2rem);
        color:#fff;
        margin:0 0 .45rem;
        line-height:1.05;
    }
    .cart-hero-sub {
        color:rgba(255,255,255,0.78);
        font-weight:400;
        max-width:640px;
        line-height:1.7;
    }
    .cart-layout {
        max-width:1100px;
        margin:0 auto;
        padding:2.5rem 2rem 5rem;
        display:grid;
        grid-template-columns:1fr 380px;
        gap:2rem;
        background:rgba(255,255,255,0.65);
        border-radius:32px;
        box-shadow:0 36px 90px rgba(28,18,9,0.08);
        backdrop-filter:blur(10px);
        border:1px solid rgba(255,255,255,0.65);
    }
    @media(max-width:980px) {
        .cart-layout {
            grid-template-columns:1fr;
        }
    }
    .card {
        background:var(--berco-surface);
        border:1px solid var(--berco-border);
        border-radius:24px;
        box-shadow:var(--berco-shadow);
        overflow:hidden;
    }
    .card-header {
        padding:1.3rem 1.6rem;
        border-bottom:1px solid var(--berco-border);
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .card-header-title {
        font-family:'Playfair Display',serif;
        font-size:1.15rem;
        letter-spacing:.01em;
        color:var(--berco-dark);
    }
    .cart-item {
        display:grid;
        grid-template-columns:88px 1fr auto;
        gap:1rem;
        align-items:center;
        padding:1.35rem 1.6rem;
        border-bottom:1px solid var(--berco-border);
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
    .cart-item-name {
        font-family:'Playfair Display',serif;
        font-weight:700;
        font-size:1rem;
        margin-bottom:.3rem;
    }
    .cart-item-meta {
        color:var(--berco-muted);
        font-size:.84rem;
        line-height:1.5;
    }
    .cart-item-notes {
        margin-top:.65rem;
        padding:.75rem 1rem;
        border-radius:14px;
        background:rgba(235,219,197,.55);
        font-size:.85rem;
        color:var(--berco-dark);
        max-width:320px;
    }
    .qty-control {
        display:flex;
        align-items:center;
        gap:.45rem;
        justify-content:flex-end;
        margin-top:.75rem;
    }
    .qty-btn {
        width:34px;
        height:34px;
        border:1px solid var(--berco-border-strong);
        border-radius:12px;
        background:#fff;
        color:var(--berco-dark);
        font-size:1.05rem;
        cursor:pointer;
        transition:background .2s,transform .2s;
    }
    .qty-btn:hover {
        background:var(--berco-cream);
        transform:translateY(-1px);
    }
    .cart-item-subtotal {
        font-weight:700;
        margin-bottom:.75rem;
        color:var(--berco-dark);
    }
    .btn-remove,
    .add-more-btn,
    .btn-explore,
    .btn-checkout,
    .btn-promo {
        border:none;
        cursor:pointer;
        transition:transform .2s,filter .2s,box-shadow .2s;
    }
    .btn-remove {
        background:transparent;
        color:#BE123C;
        font-weight:600;
        padding:.25rem .35rem;
    }
    .btn-remove:hover {
        filter:brightness(1.05);
    }
    .add-more-btn,
    .btn-explore {
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        padding:.95rem 1.25rem;
        border-radius:14px;
        background:#fff;
        border:1px solid rgba(28,18,9,.08);
        color:var(--berco-dark);
        font-weight:600;
        box-shadow:0 12px 30px rgba(28,18,9,.06);
        text-decoration:none;
    }
    .add-more-btn:hover,
    .btn-explore:hover {
        transform:translateY(-1px);
    }
    .order-notes {
        padding:1.35rem 1.6rem;
    }
    .order-notes label {
        display:block;
        font-weight:700;
        margin-bottom:.55rem;
        color:var(--berco-dark);
    }
    .order-notes textarea {
        width:100%;
        border:1px solid var(--berco-border);
        border-radius:18px;
        min-height:130px;
        padding:1rem 1.1rem;
        font-family:'DM Sans',sans-serif;
        resize:vertical;
        background:#fff;
    }
    .summary-card {
        position:sticky;
        top:1.5rem;
        align-self:start;
    }
    .summary-section {
        padding:1.35rem 1.6rem;
        border-bottom:1px solid var(--berco-border);
    }
    .summary-row {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:1rem;
        margin-bottom:.9rem;
    }
    .summary-row:last-child {
        margin-bottom:0;
    }
    .summary-section:last-child {
        border-bottom:none;
    }
    .btn-checkout {
        display:block;
        width:100%;
        padding:1.05rem 1.25rem;
        border-radius:16px;
        background:var(--berco-brown);
        color:#fff;
        font-weight:700;
        text-align:center;
        box-shadow:0 18px 24px rgba(107,63,31,.18);
        text-decoration:none;
    }
    .btn-checkout:hover {
        filter:brightness(1.04);
    }
    .summary-section:nth-child(2) {
        background:rgba(247,231,203,.35);
    }
    .promo-wrap {
        display:grid;
        grid-template-columns:1fr auto;
        gap:.75rem;
    }
    .promo-input {
        width:100%;
        border:1px solid var(--berco-border);
        border-radius:14px;
        padding:.95rem 1rem;
        background:#fff;
        color:var(--berco-dark);
    }
    .btn-promo {
        padding:.95rem 1.15rem;
        border-radius:14px;
        background:var(--berco-amber);
        color:#1C1209;
        font-weight:700;
    }
    .empty-cart {
        text-align:center;
        padding:5rem 2rem;
        background:#fff;
        border:1px solid var(--berco-border);
        border-radius:24px;
        box-shadow:var(--berco-shadow);
    }
    .empty-cart h2 {
        margin:.8rem 0 .3rem;
        font-size:2rem;
    }
    .empty-cart p {
        color:var(--berco-muted);
        max-width:36rem;
        margin:0 auto 1.5rem;
        line-height:1.8;
    }
    .alert-success {
        background:rgba(34,197,94,.12);
        border:1px solid rgba(34,197,94,.25);
        color:#166534;
        padding:1rem 1.2rem;
        border-radius:16px;
    }
    .cart-item-badge {
        display:inline-flex;
        margin-top:1.1rem;
        padding:.55rem 1rem;
        border-radius:999px;
        background:rgba(255,255,255,.12);
        color:#fff;
        font-size:.85rem;
        letter-spacing:.04em;
        border:1px solid rgba(255,255,255,.2);
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
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item?')">@csrf @method('DELETE')<button type="submit" style="background:none;border:none;color:#DC2626">Hapus semua</button></form>
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

                    <div>
                        <div class="cart-item-name">{{ $item->menu->name ?? 'Item' }}</div>
                        <div class="cart-item-meta">@if(isset($item->menu->category)) {{ $item->menu->category }} · @endif Rp {{ number_format($item->menu->price ?? 0,0,',','.') }} / item</div>
                        @if($item->notes)<div class="cart-item-notes">📝 {{ $item->notes }}</div>@endif
                    </div>

                    <div style="text-align:right">
                        <div class="cart-item-subtotal">Rp {{ number_format(($item->menu->price ?? 0) * $item->quantity,0,',','.') }}</div>
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="qty-control">@csrf @method('PATCH')<button type="submit" name="action" value="decrease" class="qty-btn">−</button><span style="padding:0 8px">{{ $item->quantity }}</span><button type="submit" name="action" value="increase" class="qty-btn">+</button></form>
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn-remove">Hapus</button></form>
                    </div>
                </div>
            @endforeach

            <a href="{{ route('menu.index') }}" class="add-more-btn">Tambah menu lainnya</a>
        </div>

        <div class="card" style="margin-top:1.25rem"><div class="order-notes"><label for="order-note">Catatan Pesanan</label><textarea id="order-note" name="order_note" rows="3">{{ old('order_note', session('order_note')) }}</textarea></div></div>
    </div>

    <div class="summary-card">
        <div class="card">
            <div class="card-header"><span class="card-header-title">Ringkasan Pesanan</span></div>
            <div class="summary-section">
                @php $subtotal = $cartItems->sum(fn($i)=>(($i->menu->price ?? 0) * $i->quantity)); $tax = round($subtotal * 0.11); $total = $subtotal + $tax; @endphp
                <div class="summary-row"><span>Subtotal ({{ $itemCount }} item)</span><span>Rp {{ number_format($subtotal,0,',','.') }}</span></div>
                <div class="summary-row"><span>PPN 11%</span><span>Rp {{ number_format($tax,0,',','.') }}</span></div>
                <div class="summary-row"><span>Biaya layanan</span><span style="color:#059669">Gratis</span></div>
                <div class="summary-row" style="font-weight:700;margin-top:.6rem"><span>Total</span><span style="color:var(--berco-gold)">Rp {{ number_format($total,0,',','.') }}</span></div>
            </div>

<div class="summary-section">
                    <div style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--berco-muted);margin-bottom:0.6rem;">Kode Promo</div>
                    <div class="promo-wrap">
                        <input type="text" name="promo_code" class="promo-input" placeholder="Masukkan kode..." disabled>
                        <button type="button" class="btn-promo" disabled>Pakai</button>
                    </div>
                    <div style="font-size:0.75rem;color:#999;margin-top:6px;">Fitur promo belum tersedia.</div>
                </div>

            <div class="summary-section"><a href="{{ route('checkout') }}" class="btn-checkout">Lanjut ke Pembayaran</a></div>
        </div>

        <div style="padding:1rem 0.5rem;color:var(--berco-muted)">Transaksi aman & terlindungi</div>
    </div>

    @else

    <div class="empty-cart"><div style="font-size:3rem">☕</div><h2>Keranjang Anda kosong</h2><p>Jelajahi menu spesial kami dan tambahkan favorit Anda ke keranjang untuk mulai memesan.</p><a href="{{ route('menu.index') }}" class="btn-explore">☕ Jelajahi Menu</a></div>

    @endif

</div>

<script>
function removeFromCart(itemId){if(confirm('Hapus item ini dari keranjang?')){fetch(`/cart/${itemId}/remove`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert('Error: '+d.message)}).catch(e=>{console.error(e);alert('Terjadi kesalahan')});}}
function updateQuantity(itemId,newQuantity){if(newQuantity<1){removeFromCart(itemId);return;}fetch(`/cart/${itemId}/update`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({quantity:newQuantity})}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert('Error: '+d.message)}).catch(e=>{console.error(e);alert('Terjadi kesalahan')});}
function increaseQuantity(itemId,currentQty){updateQuantity(itemId,currentQty+1);}function decreaseQuantity(itemId,currentQty){if(currentQty>1)updateQuantity(itemId,currentQty-1);}
</script>

@endsection
