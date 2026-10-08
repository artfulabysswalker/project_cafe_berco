@extends('Customerviews.layouts.web')

@section('title', 'Keranjang Belanja — Cafe Berco')

@section('styles')
<style>
    .cart-item-row {
        transition: all 0.3s ease;
    }
    .cart-item-row.removing {
        opacity: 0;
        transform: translateX(20px);
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
        margin: 0;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">

    {{-- 1. HEADER & BREADCRUMB --}}
    <div class="border-b border-border pb-5 pt-2 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 text-[11px] font-mono uppercase tracking-widest text-terracotta mb-1.5">
                <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                <span>Pesanan Anda • Cafe Berco</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-serif text-ink tracking-tight font-normal">Keranjang Belanja</h1>
            <p class="text-xs sm:text-sm text-ink-muted mt-1">
                Tinjau kembali pilihan menu, sesuaikan varian suhu, dan terapkan kode promo sebelum lanjut ke kasir atau checkout online.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-stone-100 border border-stone-200 rounded-lg text-xs font-mono text-ink">
                <i class="fas fa-bag-shopping text-terracotta"></i>
                <span id="header-total-items">{{ $cartItems->sum('quantity') }}</span>
                <span>Item</span>
            </span>
            <a href="{{ route('menu.index') }}" 
               class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-border hover:border-stone-400 rounded-lg text-xs font-mono text-ink transition-colors shadow-2xs">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Menu</span>
            </a>
        </div>
    </div>

    {{-- SUCCESS / INFO ALERT BANNER --}}
    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-mono text-emerald-900 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- MAIN CART CONTAINER (GRID LAYOUT) --}}
    <div id="cart-main-wrapper" class="{{ $cartItems->isEmpty() ? 'hidden' : '' }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- LEFT COLUMN: CART ITEMS LIST --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white border border-border rounded-2xl overflow-hidden shadow-2xs">
                    
                    {{-- Card Header --}}
                    <div class="px-6 py-4 border-b border-border bg-stone-50/60 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-ink"></span>
                            <h2 class="text-sm font-mono font-bold uppercase tracking-wider text-ink">
                                Daftar Pesanan (<span id="cart-items-count-label">{{ $cartItems->count() }}</span> Menu)
                            </h2>
                        </div>

                        <button type="button" 
                                onclick="clearEntireCart()"
                                class="text-xs font-mono text-rose-600 hover:text-rose-800 hover:bg-rose-50 px-2.5 py-1 rounded-md transition-colors flex items-center space-x-1.5">
                            <i class="fas fa-trash-can text-[11px]"></i>
                            <span>Hapus Semua</span>
                        </button>
                    </div>

                    {{-- Items List --}}
                    <div class="divide-y divide-stone-100" id="cart-items-list">
                        @foreach($cartItems as $item)
                            @php
                                $itemKey = $item->cart_item_key;
                                $itemMenu = $item->menu;
                                $itemImg = $item->image ?: ($itemMenu->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600');
                                $isHot = ($item->temperature === 'Hot');
                                $isIce = ($item->temperature === 'Ice' || $item->temperature === 'Cold');
                            @endphp

                            <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 cart-item-row"
                                 id="cart-row-{{ $itemKey }}"
                                 data-key="{{ $itemKey }}">
                                
                                {{-- Thumbnail & Info --}}
                                <div class="flex items-start space-x-4 flex-1">
                                    <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-xl bg-stone-100 overflow-hidden border border-border shrink-0">
                                        <img src="{{ $itemImg }}" 
                                             alt="{{ $item->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>

                                    <div class="space-y-1.5 flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm sm:text-base font-semibold text-ink leading-tight">
                                                {{ $item->name }}
                                            </h3>

                                            {{-- Variant Temperature Badges --}}
                                            @if($isHot)
                                                <span class="inline-flex items-center space-x-1 text-[11px] font-mono font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 border border-amber-300">
                                                    <span>🔥</span>
                                                    <span>Hot</span>
                                                </span>
                                            @elseif($isIce)
                                                <span class="inline-flex items-center space-x-1 text-[11px] font-mono font-bold px-2 py-0.5 rounded-md bg-sky-100 text-sky-900 border border-sky-300">
                                                    <span>🧊</span>
                                                    <span>Ice</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center space-x-1 text-[10px] font-mono px-2 py-0.5 rounded bg-stone-100 text-stone-600 border border-stone-200">
                                                    <span>{{ $item->category ?: 'Food/Snack' }}</span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs font-mono text-ink-muted flex items-center space-x-2">
                                            <span>Harga Satuan:</span>
                                            <span class="font-semibold text-ink">{{ $item->price_formatted }}</span>
                                        </div>

                                        {{-- Note input or display --}}
                                        <div class="pt-1">
                                            <div class="flex items-center space-x-2 text-[11px] font-mono">
                                                <span class="text-stone-400"><i class="fas fa-pen text-[10px]"></i></span>
                                                <input type="text" 
                                                       value="{{ $item->note }}" 
                                                       placeholder="Tambah catatan khusus (cth: Less sugar, extra ice)..."
                                                       class="item-note-input flex-1 bg-stone-50/80 hover:bg-white focus:bg-white border border-transparent hover:border-stone-300 focus:border-terracotta rounded px-2 py-0.5 text-xs text-ink focus:outline-none transition-all"
                                                       onchange="saveItemNote('{{ $itemKey }}', this.value)"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Subtotal & Quantity Counter Controls --}}
                                <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-stone-100 shrink-0">
                                    <div class="text-right">
                                        <span class="text-[10px] font-mono text-ink-muted uppercase block sm:mb-0.5">Subtotal Item</span>
                                        <span class="text-sm sm:text-base font-mono font-bold text-terracotta" id="item-subtotal-{{ $itemKey }}">
                                            {{ $item->subtotal_formatted }}
                                        </span>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        {{-- Minus / Plus Counter --}}
                                        <div class="flex items-center bg-stone-100 rounded-lg p-0.5 border border-stone-200/80">
                                            <button type="button" 
                                                    onclick="updateItemQty('{{ $itemKey }}', 'decrease')"
                                                    class="w-7 h-7 rounded-md bg-white hover:bg-stone-200 active:scale-95 flex items-center justify-center text-xs text-ink font-bold transition-all shadow-2xs"
                                                    title="Kurangi jumlah">
                                                <i class="fas fa-minus text-[9px]"></i>
                                            </button>

                                            <span class="w-8 text-center text-xs font-mono font-bold text-ink" id="item-qty-{{ $itemKey }}">
                                                {{ $item->quantity }}
                                            </span>

                                            <button type="button" 
                                                    onclick="updateItemQty('{{ $itemKey }}', 'increase')"
                                                    class="w-7 h-7 rounded-md bg-white hover:bg-stone-200 active:scale-95 flex items-center justify-center text-xs text-ink font-bold transition-all shadow-2xs"
                                                    title="Tambah jumlah">
                                                <i class="fas fa-plus text-[9px]"></i>
                                            </button>
                                        </div>

                                        {{-- Delete Button --}}
                                        <button type="button" 
                                                onclick="deleteCartItem('{{ $itemKey }}', '{{ addslashes($item->name) }}')"
                                                class="w-7 h-7 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors"
                                                title="Hapus dari keranjang">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Card Footer Actions --}}
                    <div class="p-4 bg-stone-50/70 border-t border-border flex items-center justify-between">
                        <a href="{{ route('menu.index') }}" class="text-xs font-mono font-semibold text-ink hover:text-terracotta flex items-center space-x-1.5 transition-colors">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>Lanjut Belanja Menu Lain</span>
                        </a>

                        <span class="text-[11px] font-mono text-ink-muted">
                            Pajak & diskon promo otomatis terhitung
                        </span>
                    </div>
                </div>

                {{-- Order Notes Card --}}
                <div class="bg-white border border-border rounded-2xl p-5 shadow-2xs space-y-2">
                    <label for="order-general-note" class="block text-xs font-mono font-bold uppercase tracking-wider text-ink">
                        📝 Catatan Umum Pesanan (Opsional)
                    </label>
                    <textarea id="order-general-note" 
                              rows="2" 
                              placeholder="Misal: Mohon jangan terlalu manis, sajikan di meja outdoor no. 4..."
                              class="w-full bg-stone-50 border border-stone-200 rounded-xl p-3 text-xs text-ink focus:bg-white focus:outline-none focus:ring-1 focus:ring-terracotta focus:border-terracotta transition-all"></textarea>
                </div>
            </div>

            {{-- RIGHT COLUMN: ORDER SUMMARY CARD (STICKY) --}}
            <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-20">
                
                {{-- SUMMARY CARD --}}
                <div class="bg-white border border-border rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-border bg-stone-50/80 flex items-center justify-between">
                        <h2 class="text-sm font-mono font-bold uppercase tracking-wider text-ink">
                            Ringkasan Pesanan
                        </h2>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Calculation Breakdown --}}
                        <div class="space-y-2.5 text-xs font-mono">
                            <div class="flex items-center justify-between text-ink-muted">
                                <span>Subtotal Produk</span>
                                <span class="font-semibold text-ink" id="summary-subtotal">
                                    {{ $totals['subtotal_formatted'] }}
                                </span>
                            </div>

                            {{-- Takeaway / Service Charge Note --}}
                            <div class="flex items-center justify-between text-ink-muted">
                                <span>Biaya Layanan / Takeaway</span>
                                <span class="text-[11px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-medium">
                                    Dihitung saat checkout
                                </span>
                            </div>

                            {{-- Promo Discount Row --}}
                            <div class="flex items-center justify-between text-ink-muted {{ $totals['discount'] > 0 ? '' : 'hidden' }}" id="summary-discount-row">
                                <div class="flex items-center space-x-1 text-emerald-700">
                                    <i class="fas fa-tag text-[10px]"></i>
                                    <span id="summary-discount-label">
                                        {{ $totals['promo']['name'] ?? 'Diskon Promo' }}
                                    </span>
                                </div>
                                <span class="font-bold text-emerald-600" id="summary-discount">
                                    -{{ $totals['discount_formatted'] }}
                                </span>
                            </div>

                            {{-- Tax Row (if applicable) --}}
                            @if($totals['tax'] > 0)
                                <div class="flex items-center justify-between text-ink-muted" id="summary-tax-row">
                                    <span>Pajak Restoran</span>
                                    <span class="font-semibold text-ink" id="summary-tax">
                                        {{ $totals['tax_formatted'] }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <hr class="border-border">

                        {{-- Grand Total --}}
                        <div class="pt-1 flex items-baseline justify-between">
                            <div>
                                <span class="text-xs font-mono uppercase tracking-wider text-ink-muted block">Total Pembayaran</span>
                                <span class="text-[10px] font-mono text-stone-400">Termasuk seluruh menu</span>
                            </div>
                            <span class="text-xl sm:text-2xl font-mono font-extrabold text-terracotta" id="summary-grand-total">
                                {{ $totals['grand_total_formatted'] }}
                            </span>
                        </div>

                        {{-- Promo Code Form --}}
                        <div class="pt-3 border-t border-stone-100 space-y-2">
                            <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-ink-muted">
                                Punya Kode Promo?
                            </label>

                            <div class="flex items-center space-x-2">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           id="promo-input" 
                                           name="promo_code" 
                                           value="{{ $totals['promo']['code'] ?? '' }}"
                                           placeholder="Cth: PAGI_SPESIAL" 
                                           class="w-full uppercase bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs font-mono text-ink focus:bg-white focus:outline-none focus:border-terracotta transition-all"
                                    />
                                </div>

                                <button type="button" 
                                        onclick="applyPromoCode()" 
                                        id="promo-apply-btn"
                                        class="px-3.5 py-2 bg-ink hover:bg-stone-800 text-white rounded-lg text-xs font-mono font-semibold transition-all active:scale-95">
                                    Pakai
                                </button>
                            </div>

                            {{-- Active Promo Tag (if any) --}}
                            <div id="active-promo-badge" class="{{ $totals['promo'] ? '' : 'hidden' }} flex items-center justify-between p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-mono">
                                <div class="flex items-center space-x-1.5">
                                    <i class="fas fa-circle-check text-emerald-600"></i>
                                    <span class="font-bold" id="active-promo-code-text">{{ $totals['promo']['code'] ?? '' }}</span>
                                    <span class="text-[11px] text-emerald-700">({{ $totals['promo']['name'] ?? '' }})</span>
                                </div>
                                <button type="button" onclick="removePromoCode()" class="text-emerald-700 hover:text-rose-600 ml-2" title="Hapus promo">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            {{-- Sample clickable promo pills --}}
                            <div class="pt-1 flex flex-wrap items-center gap-1.5 text-[10px] font-mono text-stone-400">
                                <span>Tersedia:</span>
                                <button type="button" onclick="setAndApplyPromo('PAGI_SPESIAL')" class="px-1.5 py-0.5 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition-colors">
                                    PAGI_SPESIAL
                                </button>
                                <button type="button" onclick="setAndApplyPromo('BERCO10')" class="px-1.5 py-0.5 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition-colors">
                                    BERCO10
                                </button>
                                <button type="button" onclick="setAndApplyPromo('LOYALITAS10')" class="px-1.5 py-0.5 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition-colors">
                                    LOYALITAS10
                                </button>
                            </div>
                        </div>

                        {{-- Checkout CTA Button --}}
                        <div class="pt-2">
                            <a href="{{ route('checkout') }}" 
                               id="checkout-btn"
                               class="w-full py-3.5 px-4 bg-terracotta hover:bg-terracotta-dark active:scale-[0.98] text-white rounded-xl font-mono text-xs uppercase tracking-wider font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-2 text-center">
                                <span>Lanjut ke Pembayaran</span>
                                <i class="fas fa-arrow-right text-[11px]"></i>
                            </a>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-stone-50 border-t border-stone-100 text-center text-[11px] font-mono text-ink-muted flex items-center justify-center space-x-2">
                        <i class="fas fa-shield-halved text-emerald-600"></i>
                        <span>Pembayaran Aman via QRIS & Kasir</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. EMPTY CART STATE --}}
    <div id="cart-empty-wrapper" class="{{ $cartItems->isNotEmpty() ? 'hidden' : '' }} py-16 px-6 text-center bg-white border border-border rounded-2xl max-w-xl mx-auto space-y-5">
        <div class="w-18 h-18 bg-stone-100 rounded-full flex items-center justify-center text-stone-400 text-3xl mx-auto border border-border">
            <i class="fas fa-bag-shopping"></i>
        </div>
        <div class="space-y-1.5">
            <h2 class="text-2xl font-serif font-semibold text-ink">Keranjang Anda Masih Kosong</h2>
            <p class="text-xs sm:text-sm text-ink-muted max-w-md mx-auto leading-relaxed">
                Anda belum memilih seduhan kopi specialty atau kudapan nikmat kami. Jelajahi menu dan tambahkan sajian favorit Anda sekarang.
            </p>
        </div>
        <div class="pt-2">
            <a href="{{ route('menu.index') }}" 
               class="inline-flex items-center space-x-2 text-xs font-mono font-bold uppercase tracking-wider bg-ink hover:bg-stone-800 text-white px-6 py-3 rounded-xl shadow-sm transition-all active:scale-95">
                <i class="fas fa-mug-hot text-terracotta"></i>
                <span>Jelajahi Menu Berco</span>
            </a>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

// Realtime Update Quantity (+ / -)
function updateItemQty(itemKey, action) {
    const qtyEl = document.getElementById(`item-qty-${itemKey}`);
    const subtotalEl = document.getElementById(`item-subtotal-${itemKey}`);
    if (!qtyEl) return;

    let currentQty = parseInt(qtyEl.textContent) || 1;
    if (action === 'decrease' && currentQty <= 1) {
        if (!confirm('Hapus menu ini dari keranjang?')) {
            return;
        }
    }

    fetch(`/cart/${itemKey}/update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ action: action })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Gagal memperbarui jumlah');
        }
        return data;
    })
    .then(data => {
        if (data.is_empty || data.cart_items_count === 0) {
            handleCartBecameEmpty();
            return;
        }

        // If item was removed because qty was decreased to 0
        if (!data.quantity || data.quantity <= 0) {
            const row = document.getElementById(`cart-row-${itemKey}`);
            if (row) {
                row.classList.add('removing');
                setTimeout(() => row.remove(), 300);
            }
        } else {
            qtyEl.textContent = data.quantity;
            if (subtotalEl && data.item_subtotal_formatted) {
                subtotalEl.textContent = data.item_subtotal_formatted;
            }
        }

        updateSummaryDisplay(data);
        if (typeof updateCartBadge === 'function') {
            updateCartBadge();
        }
    })
    .catch(err => {
        if (typeof showToast === 'function') {
            showToast(err.message, 'error', null);
        } else {
            alert(err.message);
        }
    });
}

// Realtime Delete Single Item
function deleteCartItem(itemKey, itemName) {
    if (!confirm(`Hapus "${itemName}" dari keranjang?`)) {
        return;
    }

    const row = document.getElementById(`cart-row-${itemKey}`);
    if (row) {
        row.classList.add('removing');
    }

    fetch(`/cart/${itemKey}/remove`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Gagal menghapus item');
        }
        return data;
    })
    .then(data => {
        if (row) {
            setTimeout(() => row.remove(), 300);
        }

        if (data.is_empty || data.cart_items_count === 0) {
            handleCartBecameEmpty();
        } else {
            updateSummaryDisplay(data);
        }

        if (typeof updateCartBadge === 'function') {
            updateCartBadge();
        }

        if (typeof showToast === 'function') {
            showToast(data.message || 'Item dihapus', 'info', null);
        }
    })
    .catch(err => {
        if (row) {
            row.classList.remove('removing');
        }
        if (typeof showToast === 'function') {
            showToast(err.message, 'error', null);
        } else {
            alert(err.message);
        }
    });
}

// Clear Entire Cart
function clearEntireCart() {
    if (!confirm('Kosongkan seluruh isi keranjang belanja?')) {
        return;
    }

    fetch('{{ route("cart.clear") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Gagal mengosongkan keranjang');
        }
        return data;
    })
    .then(data => {
        handleCartBecameEmpty();
        if (typeof updateCartBadge === 'function') {
            updateCartBadge();
        }
        if (typeof showToast === 'function') {
            showToast('Keranjang belanja berhasil dikosongkan.', 'info', null);
        }
    })
    .catch(err => {
        if (typeof showToast === 'function') {
            showToast(err.message, 'error', null);
        } else {
            alert(err.message);
        }
    });
}

// Save Item Note
function saveItemNote(itemKey, noteText) {
    fetch(`/cart/${itemKey}/note`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ note: noteText })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && typeof showToast === 'function') {
            showToast('Catatan menu disimpan.', 'success', null);
        }
    })
    .catch(() => {});
}

// Apply Promo Code
function applyPromoCode() {
    const input = document.getElementById('promo-input');
    const code = input ? input.value.trim() : '';
    const btn = document.getElementById('promo-apply-btn');

    if (!code) {
        if (typeof showToast === 'function') {
            showToast('Silakan masukkan kode promo.', 'error', null);
        }
        return;
    }

    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }

    fetch('{{ route("cart.promo.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ promo_code: code })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Kode promo tidak valid');
        }
        return data;
    })
    .then(data => {
        if (btn) {
            btn.innerHTML = 'Pakai';
            btn.disabled = false;
        }

        if (data.totals) {
            updateSummaryDisplay(data.totals);
        }

        // Show active promo badge
        const badge = document.getElementById('active-promo-badge');
        const codeText = document.getElementById('active-promo-code-text');
        if (badge && data.promo) {
            badge.classList.remove('hidden');
            if (codeText) codeText.textContent = data.promo.code;
        }

        if (typeof showToast === 'function') {
            showToast(data.message || 'Kode promo berhasil dipasang!', 'success', null);
        }
    })
    .catch(err => {
        if (btn) {
            btn.innerHTML = 'Pakai';
            btn.disabled = false;
        }
        if (typeof showToast === 'function') {
            showToast(err.message, 'error', null);
        } else {
            alert(err.message);
        }
    });
}

function setAndApplyPromo(code) {
    const input = document.getElementById('promo-input');
    if (input) input.value = code;
    applyPromoCode();
}

// Remove Promo Code
function removePromoCode() {
    fetch('{{ route("cart.promo.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => r.json())
    .then(data => {
        const badge = document.getElementById('active-promo-badge');
        const input = document.getElementById('promo-input');
        if (badge) badge.classList.add('hidden');
        if (input) input.value = '';

        if (data.totals) {
            updateSummaryDisplay(data.totals);
        }

        if (typeof showToast === 'function') {
            showToast('Kode promo dihapus.', 'info', null);
        }
    })
    .catch(() => {});
}

// Update Summary Elements in DOM
function updateSummaryDisplay(data) {
    const subtotalEl = document.getElementById('summary-subtotal');
    const grandTotalEl = document.getElementById('summary-grand-total');
    const discountRow = document.getElementById('summary-discount-row');
    const discountEl = document.getElementById('summary-discount');
    const discountLabel = document.getElementById('summary-discount-label');
    const headerItemsEl = document.getElementById('header-total-items');
    const itemsCountLabel = document.getElementById('cart-items-count-label');

    if (subtotalEl && data.subtotal_formatted) subtotalEl.textContent = data.subtotal_formatted;
    if (grandTotalEl && data.grand_total_formatted) grandTotalEl.textContent = data.grand_total_formatted;
    if (headerItemsEl && data.cart_count !== undefined) headerItemsEl.textContent = data.cart_count;
    if (itemsCountLabel && data.cart_items_count !== undefined) itemsCountLabel.textContent = data.cart_items_count;

    if (discountRow) {
        if (data.discount && data.discount > 0) {
            discountRow.classList.remove('hidden');
            if (discountEl && data.discount_formatted) discountEl.textContent = '-' + data.discount_formatted;
            if (discountLabel && data.promo && data.promo.name) discountLabel.textContent = data.promo.name;
        } else {
            discountRow.classList.add('hidden');
        }
    }
}

// Transition to empty cart state
function handleCartBecameEmpty() {
    const mainWrapper = document.getElementById('cart-main-wrapper');
    const emptyWrapper = document.getElementById('cart-empty-wrapper');
    const headerItemsEl = document.getElementById('header-total-items');

    if (mainWrapper) mainWrapper.classList.add('hidden');
    if (emptyWrapper) emptyWrapper.classList.remove('hidden');
    if (headerItemsEl) headerItemsEl.textContent = '0';
}
</script>
@endsection
