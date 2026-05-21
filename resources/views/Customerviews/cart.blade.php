@extends('layouts.app')

@section('title', 'Keranjang Belanja - Berco Cafe')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root { --berco-brown:#6B3F1F; --berco-amber:#D97706; --berco-cream:#FDF6ED; --berco-warm:#F5E6D3; --berco-dark:#1C1209; --berco-muted:#8B6B4A; --berco-gold:#B45309; --berco-surface:#FFFBF5; --berco-border:rgba(107,63,31,0.12); --berco-border-strong:rgba(107,63,31,0.25); }
    body{font-family:'DM Sans',sans-serif;background:var(--berco-cream);color:var(--berco-dark)}
    .cart-hero{background:var(--berco-brown);padding:3rem 0 2.5rem}
    .cart-hero-inner{max-width:1100px;margin:0 auto;padding:0 2rem}
    .cart-hero-label{font-size:11px;font-weight:600;color:rgba(255,255,255,0.55);margin-bottom:.5rem}
    .cart-hero h1{font-family:'Playfair Display',serif;font-size:clamp(2rem,4vw,2.8rem);color:#fff;margin:0 0 .35rem}
    .cart-hero-sub{color:rgba(255,255,255,0.6);font-weight:300}
    .cart-layout{max-width:1100px;margin:0 auto;padding:2.5rem 2rem 5rem;display:grid;grid-template-columns:1fr 380px;gap:2rem}
    @media(max-width:900px){.cart-layout{grid-template-columns:1fr}}
    .card{background:var(--berco-surface);border:1px solid var(--berco-border);border-radius:18px}
    .card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--berco-border);display:flex;justify-content:space-between}
    .cart-item{display:grid;grid-template-columns:80px 1fr auto;gap:1rem;align-items:center;padding:1.25rem 1.5rem;border-bottom:1px solid var(--berco-border)}
    .cart-item-img{width:80px;height:80px;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:var(--berco-warm);border:1px solid var(--berco-border)}
    .cart-item-name{font-family:'Playfair Display',serif;font-weight:600}
    .cart-item-meta{color:var(--berco-muted);font-size:.78rem}
    .qty-control{display:flex;align-items:center}
    .qty-btn{width:32px;height:32px;border:none;background:transparent;cursor:pointer}
    .summary-card{position:sticky;top:1.5rem}
    .summary-section{padding:1.25rem 1.5rem;border-bottom:1px solid var(--berco-border)}
    .summary-row{display:flex;justify-content:space-between;align-items:center}
    .btn-checkout{display:block;width:100%;padding:1rem;background:var(--berco-brown);color:#fff;border-radius:14px;text-align:center}
    .empty-cart{text-align:center;padding:5rem 2rem}
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

            <div class="summary-section"><form action="{{ route('cart.promo') }}" method="POST">@csrf<div class="promo-wrap"><input type="text" name="promo_code" value="{{ session('promo_code') }}"><button type="submit" class="btn-promo">Pakai</button></div></form>@if(session('promo_error'))<div style="color:#DC2626">{{ session('promo_error') }}</div>@endif@if(session('promo_success'))<div style="color:#059669">✓ {{ session('promo_success') }}</div>@endif</div>

            <div class="summary-section"><a href="{{ route('checkout.index') }}" class="btn-checkout">Lanjut ke Pembayaran</a></div>
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
