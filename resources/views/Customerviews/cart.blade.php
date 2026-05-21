@extends('Customerviews.layouts.web')

@section('title', 'Keranjang - Berco Cafe')

@section('content')
<style>
    @keyframes slideInCart {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .cart-item {
        animation: slideInCart 0.4s ease-out;
    }
    
    .hover-scale {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hover-scale:hover {
        transform: translateY(-2px);
    }
    
    .quantity-btn {
        transition: all 0.2s ease;
    }
    
    .quantity-btn:hover {
        background-color: rgba(139, 62, 0, 0.1);
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-[#F7F0D9] via-[#FBF7EE] to-[#F5F0E1] py-12">
    <main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-10 rounded-[2.5rem] bg-gradient-to-r from-white to-orange-50 p-8 shadow-[0_25px_50px_rgba(151,85,43,0.1)] ring-1 ring-orange-200/50">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.4em] text-[#8B3E00]">🛒 Keranjang Belanja</p>
                    <h1 class="mt-4 text-4xl font-black text-slate-900">Pesananmu</h1>
                    <p class="mt-3 text-base text-slate-600">Kelola item pilihan dan lanjutkan ke pembayaran dengan mudah.</p>
                </div>
                <a href="{{ route('menu.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#8B3E00] to-[#a0480f] px-6 py-4 text-sm font-bold text-white shadow-lg shadow-orange-300/40 transition-all duration-300 hover:shadow-xl hover:shadow-orange-300/60 hover:-translate-y-1">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Menu
                </a>
            </div>
        </div>

        @if($cartItems->isEmpty())
            <div class="rounded-[2.5rem] bg-white p-16 text-center shadow-[0_20px_40px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-orange-100 to-orange-50 text-5xl text-[#A16207]">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2 class="mt-8 text-3xl font-bold text-slate-900">Keranjang Anda kosong</h2>
                <p class="mt-4 text-base text-slate-600">Jelajahi menu spesial kami dan tambahkan item favorit ke keranjang untuk memulai pesanan.</p>
                <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center sm:gap-4">
                    <a href="{{ route('menu.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#8B3E00] to-[#a0480f] px-8 py-4 text-base font-bold text-white transition-all duration-300 hover:shadow-lg hover:shadow-orange-300/50 hover:-translate-y-1">
                        <i class="fas fa-arrow-right"></i>
                        Jelajahi Menu
                    </a>
                    <a href="{{ route('menu.index') }}?price=low" class="inline-flex items-center justify-center gap-2 rounded-full border-2 border-orange-200 bg-white px-8 py-4 text-base font-bold text-[#8B3E00] transition-all duration-300 hover:bg-orange-50">
                        <i class="fas fa-tag"></i>
                        Menu Murah
                    </a>
                </div>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
                <!-- Cart Items List -->
                <section class="space-y-4">
                    <div class="mb-6">
                        <p class="text-sm font-bold uppercase tracking-[0.3em] text-slate-500">Item Pesanan</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $cartItems->count() }} Item</h2>
                    </div>
                    
                    @foreach($cartItems as $item)
                        <article class="cart-item group rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover-scale hover:shadow-lg hover:border-orange-300">
                            <div class="grid gap-5 sm:grid-cols-[120px_minmax(0,1fr)_auto]">
                                <!-- Product Image -->
                                <div class="h-32 w-full overflow-hidden rounded-[1.5rem] bg-gradient-to-br from-slate-100 to-slate-50 ring-1 ring-slate-200">
                                    <img src="{{ $item->menu->image_url ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?q=80&w=300' }}" alt="{{ $item->menu->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" />
                                </div>

                                <!-- Product Details -->
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900">{{ $item->menu->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-600">{{ Str::limit($item->menu->description ?? 'Premium coffee choice', 80) }}</p>
                                    </div>

                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                        <!-- Quantity Control -->
                                        <div class="inline-flex items-center rounded-full border-2 border-slate-200 bg-slate-50">
                                            <button type="button" onclick="decreaseQuantity({{ $item->id }}, {{ $item->quantity }})" class="quantity-btn h-11 w-11 text-base font-bold text-slate-900">−</button>
                                            <input type="number" class="w-14 bg-transparent text-center text-base font-bold text-slate-900 outline-none" value="{{ $item->quantity }}" data-item-id="{{ $item->id }}" onchange="updateQuantity({{ $item->id }}, this.value)" min="1" max="100" />
                                            <button type="button" onclick="increaseQuantity({{ $item->id }}, {{ $item->quantity }})" class="quantity-btn h-11 w-11 text-base font-bold text-slate-900">+</button>
                                        </div>

                                        <!-- Price Info -->
                                        <div class="flex items-baseline gap-3">
                                            <span class="text-sm text-slate-600">Harga:</span>
                                            <p class="rounded-full bg-gradient-to-r from-orange-100 to-amber-100 px-4 py-2 text-base font-bold text-[#8B3E00]">Rp {{ number_format($item->menu->harga, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Button -->
                                <div class="flex items-start justify-end">
                                    <button type="button" onclick="removeFromCart({{ $item->id }})" class="group/btn inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600 transition-all duration-300 hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-300/50">
                                        <i class="fas fa-trash text-base"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Subtotal Bar -->
                            <div class="mt-5 border-t border-slate-200 pt-4 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-600">Subtotal</span>
                                <span class="text-xl font-bold text-slate-900">Rp {{ number_format($item->menu->harga * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        </article>
                    @endforeach
                </section>

                <!-- Summary Sidebar -->
                <aside class="h-fit rounded-[1.75rem] border border-slate-200 bg-gradient-to-b from-white to-orange-50 p-7 shadow-[0_10px_30px_rgba(15,23,42,0.08)] ring-1 ring-orange-100/50 lg:sticky lg:top-24">
                    <div class="space-y-6">
                        <!-- Header -->
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">📋 Ringkasan</p>
                            <h2 class="mt-3 text-2xl font-black text-slate-900">Total Belanja</h2>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 rounded-[1.25rem] bg-white p-5 ring-1 ring-slate-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Subtotal</span>
                                <span class="font-semibold text-slate-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Pajak (10%)</span>
                                <span class="font-semibold text-slate-900">Rp {{ number_format($total * 0.1, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-slate-200 pt-4 flex items-center justify-between">
                                <span class="font-bold text-slate-900">Total</span>
                                <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#8B3E00] to-[#d97706]">Rp {{ number_format($total * 1.1, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 pt-2">
                            <a href="{{ route('checkout') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#8B3E00] to-[#a0480f] px-5 py-4 text-base font-bold text-white shadow-lg shadow-orange-300/40 transition-all duration-300 hover:shadow-xl hover:shadow-orange-300/60 hover:-translate-y-1">
                                <i class="fas fa-credit-card"></i>
                                Lanjut Pembayaran
                            </a>
                            <a href="{{ route('menu.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full border-2 border-slate-200 bg-white px-5 py-4 text-base font-bold text-slate-900 transition-all duration-300 hover:bg-slate-50 hover:border-slate-300">
                                <i class="fas fa-plus"></i>
                                Tambah Menu
                            </a>
                        </div>

                        <!-- Info Box -->
                        <div class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200">
                            <p class="text-xs font-semibold text-amber-900">✨ Free ongkir untuk pembelian di atas Rp 100.000</p>
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </main>
</div>

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
