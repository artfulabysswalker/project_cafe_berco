@extends('Customerviews.layouts.web')

@section('title', 'Katalog Menu — Cafe Berco')

@section('content')
<div class="space-y-8">

    {{-- 1. HERO BANNER & SEARCH/FILTER TOOLBAR --}}
    <div class="border-b border-border pb-6 pt-2 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 text-[11px] font-mono uppercase tracking-widest text-terracotta mb-1.5">
                <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                <span>Katalog Seduhan & Kudapan Cafe Berco</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-serif text-ink tracking-tight font-normal">Daftar Menu Berco</h1>
            <p class="text-xs sm:text-sm text-ink-muted mt-1 max-w-xl">
                Seduhan biji specialty pilihan, aneka varian espresso, sajian non-kopi segar, kudapan, dan hidangan hangat dipanggang setiap hari.
            </p>
        </div>

        {{-- Search & Price Filter Form --}}
        <form method="GET" action="{{ route('menu.index') }}" class="flex items-center space-x-2 w-full md:w-auto">
            @if(request('category') && request('category') !== 'all')
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <div class="relative flex-1 md:w-64">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari menu, rasa, series..." 
                    class="w-full bg-white border border-border rounded-md pl-8 pr-8 py-2 text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta focus:border-terracotta transition-shadow"
                />
                <i class="fas fa-magnifying-glass text-stone-400 text-xs absolute left-2.5 top-3"></i>
                @if(request('search'))
                    <a href="{{ route('menu.index', array_filter(['category' => request('category'), 'price' => request('price')])) }}" 
                       class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-600 text-xs" 
                       title="Hapus pencarian">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </div>

            <select name="price" onchange="this.form.submit()" class="bg-white border border-border rounded-md px-3 py-2 text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta">
                <option value="all">Semua Harga</option>
                <option value="low" {{ request('price') === 'low' ? 'selected' : '' }}>&lt; Rp 15.000</option>
                <option value="high" {{ request('price') === 'high' ? 'selected' : '' }}>&ge; Rp 15.000</option>
            </select>
        </form>
    </div>

    {{-- 2. CATEGORY NAVIGATION TABS (STICKY & RESPONSIVE) --}}
    <div class="sticky top-16 z-20 bg-canvas/95 backdrop-blur-md py-2.5 border-b border-border/80 -mx-4 px-4 sm:-mx-6 sm:px-6 flex items-center space-x-2 overflow-x-auto text-xs font-mono scrollbar-none">
        @php
            $isAllActive = empty(request('category')) || request('category') === 'all';
            $allTotal = $allCategories->sum('products_count');
        @endphp

        {{-- TAB "SEMUA" --}}
        <a href="{{ route('menu.index', array_filter(['search' => request('search'), 'price' => request('price')])) }}" 
           class="px-4 py-2 rounded-lg transition-all whitespace-nowrap flex items-center space-x-1.5 {{ $isAllActive ? 'bg-ink text-white font-medium shadow-2xs' : 'bg-white border border-border text-ink-muted hover:text-ink hover:border-stone-400' }}">
            <span>SEMUA</span>
            <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $isAllActive ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-600' }}">
                {{ $allTotal }}
            </span>
        </a>

        {{-- TAB PER KATEGORI --}}
        @foreach($allCategories as $catItem)
            @php
                $catId = $catItem->id;
                $catSlug = $catItem->slug;
                $catParam = request('category');
                $isCatActive = ($catParam == $catId || $catParam == $catSlug);
            @endphp
            <a href="{{ route('menu.index', array_filter(['category' => $catSlug, 'search' => request('search'), 'price' => request('price')])) }}" 
               class="px-4 py-2 rounded-lg transition-all whitespace-nowrap flex items-center space-x-2 {{ $isCatActive ? 'bg-ink text-white font-medium shadow-2xs' : 'bg-white border border-border text-ink-muted hover:text-ink hover:border-stone-400' }}">
                @if(!empty($catItem->icon))
                    <i class="{{ $catItem->icon }} text-[11px] {{ $isCatActive ? 'text-amber-400' : 'text-stone-400' }}"></i>
                @endif
                <span>{{ strtoupper($catItem->nama_kategori) }}</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $isCatActive ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-600' }}">
                    {{ $catItem->products_count }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- 3. SECTIONS PER KATEGORI & SUB-HEADER SERIES --}}
    @php
        $hasAnyProducts = false;
        
        // Fallback photos map for Cafe Berco products
        $fallbackImages = [
            'Americano' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=600',
            'Long Black' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?q=80&w=600',
            'Cappuccino' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?q=80&w=600',
            'Caffe Latte' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?q=80&w=600',
            'Magic' => 'https://images.unsplash.com/photo-1585494156145-1c60a4fe9d2b?q=80&w=600',
            'Kopi Susu Hazelnut' => 'https://images.unsplash.com/photo-1577968897966-3d4325b36b61?q=80&w=600',
            'Kopi Susu Caramel' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=600',
            'Kopi Susu Vanilla' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600',
            'Kopi Susu Butterscotch' => 'https://images.unsplash.com/photo-1497636577773-f1231844b336?q=80&w=600',
            'Kopi Susu Gula Aren' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=600',
            'Kopi Susu Moccacino' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?q=80&w=600',
            'Kopi Susu Berco' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=600',
            'Lemonade Americano' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?q=80&w=600',
            'Summer Strawberry' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?q=80&w=600',
            'Summer Lychee' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?q=80&w=600',
            'Summer Orange' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?q=80&w=600',
            'Sparkling Strawberry' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?q=80&w=600',
            'Sparkling Lychee' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?q=80&w=600',
            'Sparkling Orange' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?q=80&w=600',
            'Lychee Tea' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=600',
            'Lemon Tea' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?q=80&w=600',
            'Berry Tea' => 'https://images.unsplash.com/photo-1597481499750-3e6b22637e12?q=80&w=600',
            'Chocolate Milk' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?q=80&w=600',
            'Taro Milk' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?q=80&w=600',
            'Green Tea Milk' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?q=80&w=600',
            'Matcha Latte' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?q=80&w=600',
            'Matcha Strawberry' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?q=80&w=600',
            'Matcha Orange' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?q=80&w=600',
            'Matchacano' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=600',
            'Jahe Hangat' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=600',
            'Susu Jahe' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=600',
            'Jhosua' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?q=80&w=600',
            'Sogem' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?q=80&w=600',
            'Nasi Goreng Jawa' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?q=80&w=600',
            'Nasi Goreng Seafood' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?q=80&w=600',
            'Mie Nyemek' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=600',
            'Mie Goreng' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=600',
            'Kwetiau' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?q=80&w=600',
            'Chicken Blackpaper' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=600',
            'Ayam Chili Padi' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=600',
            'French Fries' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?q=80&w=600',
            'Nugget Ayam' => 'https://images.unsplash.com/photo-1562967914-608f82629710?q=80&w=600',
            'Mix Snack' => 'https://images.unsplash.com/photo-1562967914-608f82629710?q=80&w=600',
            'Risol Original' => 'https://images.unsplash.com/photo-1608897013039-887f21d8c804?q=80&w=600',
            'Risol Mayones' => 'https://images.unsplash.com/photo-1608897013039-887f21d8c804?q=80&w=600',
            'Lumpia Sayur' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?q=80&w=600',
            'Cireng Salju + Saus Bangkok' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?q=80&w=600',
            'Tahu Walik' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600',
            'Tahu Petis' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600',
            'Sosis Bakar' => 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?q=80&w=600',
            'Donut Berco Original' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?q=80&w=600',
            'Donut Berco Chocolate' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?q=80&w=600',
            'Donut Berco Matcha' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?q=80&w=600',
            'Donut Berco Tiramisu' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?q=80&w=600',
            'Churros Original' => 'https://images.unsplash.com/photo-1624300629298-e9de39c13be5?q=80&w=600',
            'Pisang Chocolate Original' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?q=80&w=600',
        ];
    @endphp

    <div class="space-y-16">
        @foreach($categories as $category)
            @if($category->products->isNotEmpty())
                @php 
                    $hasAnyProducts = true;
                    // Group products in this category by series
                    $groupedSeries = $category->products->groupBy(function($prod) use ($category) {
                        return !empty($prod->series) ? $prod->series : $category->nama_kategori;
                    });
                @endphp

                <section id="category-{{ $category->slug }}" class="scroll-mt-32 space-y-8">
                    
                    {{-- Category Main Section Header --}}
                    <div class="flex items-center justify-between pb-3 border-b-2 border-stone-800">
                        <div class="flex items-center space-x-3">
                            <span class="w-9 h-9 rounded-xl bg-ink text-white flex items-center justify-center text-sm shadow-sm">
                                <i class="{{ $category->icon ?: 'fas fa-mug-saucer' }}"></i>
                            </span>
                            <div>
                                <h2 class="text-2xl font-serif font-semibold text-ink tracking-tight">
                                    {{ $category->nama_kategori }}
                                </h2>
                                <p class="text-[11px] font-mono text-ink-muted">
                                    Total {{ $category->products->count() }} Pilihan Menu
                                </p>
                            </div>
                        </div>

                        <span class="text-[10px] font-mono uppercase tracking-widest text-ink-muted hidden sm:inline-block">
                            Section / {{ $category->slug }}
                        </span>
                    </div>

                    {{-- Series Sub-Sections --}}
                    <div class="space-y-8">
                        @foreach($groupedSeries as $seriesName => $seriesProducts)
                            <div class="space-y-4">
                                
                                {{-- Series Sub-Header (Misal: "Series: Black", "Series: White", dll.) --}}
                                @if($seriesName && $seriesName !== $category->nama_kategori)
                                    <div class="flex items-center space-x-2.5 pt-1">
                                        <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-ink">
                                            Series: {{ $seriesName }}
                                        </h3>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-stone-100 text-stone-600 border border-stone-200">
                                            {{ $seriesProducts->count() }} menu
                                        </span>
                                        <div class="flex-1 border-t border-border/70"></div>
                                    </div>
                                @endif

                                {{-- Product Cards Grid --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                                    @foreach($seriesProducts as $product)
                                        @php
                                            $prodId = $product->id_menu ?? $product->id;
                                            $prodName = $product->nama_menu ?? $product->name;
                                            $prodPrice = $product->harga ?? $product->price;
                                            $prodDesc = $product->deskripsi ?? $product->description;
                                            
                                            // Image resolution
                                            $prodImg = $product->foto 
                                                ? asset('storage/' . $product->foto) 
                                                : ($fallbackImages[$prodName] ?? ($product->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600'));
                                            
                                            $prodStock = $product->stok ?? 50;
                                            $isFav = in_array($prodId, $favoriteIds ?? []);
                                        @endphp

                                        <div class="bg-white border border-border rounded-xl overflow-hidden hover:border-stone-400 hover:shadow-md transition-all flex flex-col justify-between group">
                                            
                                            {{-- Thumbnail & Badges --}}
                                            <div class="relative h-44 bg-stone-100 overflow-hidden">
                                                <img src="{{ $prodImg }}" 
                                                     alt="{{ $prodName }}" 
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                                     loading="lazy">
                                                
                                                {{-- Series / Category Badge --}}
                                                <span class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-xs border border-border px-2 py-0.5 rounded text-[10px] font-mono font-semibold text-ink shadow-2xs">
                                                    {{ $product->series ?: $category->nama_kategori }}
                                                </span>

                                                {{-- Wishlist / Favorite Button --}}
                                                @auth
                                                    <button class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white/90 backdrop-blur-xs border border-border flex items-center justify-center text-xs transition-colors shadow-2xs {{ $isFav ? 'text-rose-600' : 'text-stone-400 hover:text-rose-600' }}"
                                                            onclick="toggleFavorite({{ $prodId }}, this)"
                                                            type="button"
                                                            title="Favoritkan">
                                                        <i class="{{ $isFav ? 'fas' : 'far' }} fa-heart"></i>
                                                    </button>
                                                @endauth

                                                {{-- Out of Stock Overlay --}}
                                                @if(isset($prodStock) && $prodStock <= 0)
                                                    <div class="absolute inset-0 bg-stone-900/70 backdrop-blur-2xs flex items-center justify-center">
                                                        <span class="px-3 py-1 rounded bg-red-600 text-white text-[11px] font-mono tracking-wider font-semibold shadow-sm">
                                                            HABIS
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Meta Details & Actions --}}
                                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                                <div>
                                                    {{-- Product Name --}}
                                                    <h4 class="text-sm font-semibold text-ink leading-snug group-hover:text-terracotta transition-colors line-clamp-1" title="{{ $prodName }}">
                                                        {{ $prodName }}
                                                    </h4>

                                                    {{-- Temperature Option Badge --}}
                                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                                        @if($product->has_temperature_option)
                                                            <span class="inline-flex items-center text-[10px] font-mono bg-amber-50 text-amber-900 border border-amber-200/80 px-2 py-0.5 rounded" title="Tersedia Hot & Cold">
                                                                <i class="fas fa-temperature-half text-[9px] mr-1 text-amber-600"></i> Hot / Cold
                                                            </span>
                                                        @elseif(!empty($product->temperature_options) && count($product->temperature_options) == 1)
                                                            <span class="inline-flex items-center text-[10px] font-mono bg-stone-50 text-stone-700 border border-stone-200 px-2 py-0.5 rounded">
                                                                <i class="fas {{ $product->temperature_options[0]['type'] === 'Hot' ? 'fa-fire text-amber-600' : 'fa-snowflake text-sky-600' }} text-[9px] mr-1"></i>
                                                                {{ $product->temperature_options[0]['type'] }} Only
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Description --}}
                                                    <p class="text-xs text-ink-muted mt-2 line-clamp-2 leading-relaxed">
                                                        {{ $prodDesc ?: 'Seduhan biji specialty terstandar dan racikan bahan pilihan khas Cafe Berco.' }}
                                                    </p>
                                                </div>

                                                {{-- Price & Order Action --}}
                                                <div class="flex items-center justify-between pt-3 border-t border-stone-100">
                                                    <div class="flex flex-col">
                                                        <span class="font-mono text-sm font-bold text-ink">
                                                            {{ $product->price_range_formatted ?? ('Rp ' . number_format($prodPrice, 0, ',', '.')) }}
                                                        </span>
                                                        @if(!empty($product->temperature_options) && count($product->temperature_options) > 1)
                                                            <span class="text-[9px] font-mono text-stone-400">Pilihan suhu variatif</span>
                                                        @endif
                                                    </div>

                                                    @if(isset($prodStock) && $prodStock <= 0)
                                                        <button class="px-3 py-1.5 text-xs font-mono bg-stone-100 text-stone-400 rounded-md cursor-not-allowed" disabled>
                                                            Habis
                                                        </button>
                                                    @else
                                                        <button 
                                                            type="button"
                                                            class="add-to-cart-btn px-3.5 py-1.5 text-xs font-mono font-medium bg-canvas hover:bg-ink hover:text-white text-ink rounded-lg border border-border transition-all inline-flex items-center space-x-1.5 shadow-2xs hover:shadow-xs active:scale-95"
                                                            data-id="{{ $prodId }}"
                                                            data-name="{{ $prodName }}"
                                                        >
                                                            <i class="fas fa-plus text-[10px]"></i>
                                                            <span>Pesan</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                </section>
            @endif
        @endforeach
    </div>

    {{-- 4. EMPTY STATE (Jika tidak ada produk ditemukan) --}}
    @if(!$hasAnyProducts)
        <div class="py-16 px-6 text-center bg-white border border-border rounded-xl space-y-4">
            <div class="w-14 h-14 bg-stone-100 rounded-full flex items-center justify-center text-stone-400 text-xl mx-auto border border-border">
                <i class="fas fa-mug-saucer"></i>
            </div>
            <div>
                <h3 class="text-base font-serif font-medium text-ink">Menu Tidak Ditemukan</h3>
                <p class="text-xs text-ink-muted mt-1 max-w-sm mx-auto">
                    Tidak ada menu yang sesuai dengan kata kunci atau filter harga yang Anda pilih.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('menu.index') }}" 
                   class="inline-flex items-center space-x-2 text-xs font-mono font-medium bg-ink text-white px-4 py-2 rounded-lg hover:bg-stone-800 transition-colors">
                    <i class="fas fa-rotate-left text-[11px]"></i>
                    <span>Reset Semua Filter</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
// Toggle Wishlist / Favorite AJAX
function toggleFavorite(productId, buttonElement) {
    fetch('{{ route("favorites.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ menu_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        const icon = buttonElement.querySelector('i');
        if (data.status === 'added') {
            buttonElement.classList.add('text-rose-600');
            buttonElement.classList.remove('text-stone-400');
            icon.classList.replace('far', 'fas');
        } else if (data.status === 'removed') {
            buttonElement.classList.remove('text-rose-600');
            buttonElement.classList.add('text-stone-400');
            icon.classList.replace('fas', 'far');
        }
    })
    .catch(() => {});
}

// Add to Cart AJAX with Toast Feedback
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name') || 'Menu';
            const originalHtml = this.innerHTML;

            this.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i> <span>Menambah...</span>';
            this.disabled = true;

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    menu_id: productId,
                    quantity: 1
                })
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menambahkan ke keranjang');
                }
                return data;
            })
            .then(data => {
                // Button feedback
                this.innerHTML = '<i class="fas fa-check text-emerald-600 text-[10px]"></i> <span>Ditambah!</span>';
                this.classList.add('bg-emerald-50', 'border-emerald-300', 'text-emerald-900');
                
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('bg-emerald-50', 'border-emerald-300', 'text-emerald-900');
                    this.disabled = false;
                }, 1200);

                // Update navbar cart badge in real-time
                if (typeof updateCartBadge === 'function') {
                    updateCartBadge();
                }

                // Trigger Floating Toast Notification
                if (typeof showToast === 'function') {
                    showToast(data.message || `Menu "${productName}" berhasil ditambahkan ke keranjang!`, 'success', '{{ route('cart.index') }}', 'Lihat Keranjang');
                }
            })
            .catch(err => {
                this.innerHTML = originalHtml;
                this.disabled = false;

                if (typeof showToast === 'function') {
                    showToast(err.message || 'Terjadi kesalahan saat menambahkan ke keranjang.', 'error', null);
                } else {
                    alert(err.message || 'Gagal menambahkan ke keranjang.');
                }
            });
        });
    });
});
</script>
@endsection
