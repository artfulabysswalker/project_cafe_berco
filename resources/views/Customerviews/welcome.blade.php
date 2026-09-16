<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berco Cafe — Specialty Coffee & Artisan Bites Banyuwangi</title>
    <meta name="description" content="Kopi specialty lokal Tanah Blambangan Banyuwangi, suasana warm & cozy, hidangan lezat berkelas.">
    
    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        coffee: {
                            900: '#140A04',
                            800: '#1F1107',
                            700: '#2E190B',
                            primary: '#C26A26',
                            'primary-dark': '#9A4C13',
                            amber: '#E07A28',
                            cream: '#FAF5EE',
                            'cream-card': '#FFFFFF',
                            border: '#E8DED2',
                            text: '#241409',
                            muted: '#7A6455',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Smooth Infinite Sliding Marquee Track */
        @keyframes marqueeScrollLeft {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .marquee-track-auto {
            display: flex;
            width: max-content;
            animation: marqueeScrollLeft 32s linear infinite;
        }

        .marquee-viewport-wrap:hover .marquee-track-auto {
            animation-play-state: paused;
        }

        /* Pop-up / Timbul Card Effect */
        .favorite-card-pop {
            transition: transform 0.28s cubic-bezier(0.34, 1.4, 0.64, 1), box-shadow 0.28s ease, border-color 0.28s ease;
        }

        .favorite-card-pop:hover {
            transform: translateY(-8px) scale(1.025);
            box-shadow: 0 16px 32px -6px rgba(36, 20, 9, 0.18);
            border-color: #C26A26;
            z-index: 20;
        }
    </style>
</head>
<body class="bg-coffee-cream text-coffee-text antialiased font-sans selection:bg-stone-200">

    {{-- 1. NAVBAR (WARM DARK ESPRESSO) --}}
    <nav class="sticky top-0 z-50 bg-coffee-900/95 backdrop-blur-xs border-b border-white/10 text-white">
        <div class="max-w-7xl mx-auto px-6 h-18 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-lg bg-coffee-primary flex items-center justify-center text-white font-serif font-bold text-base shadow-sm">
                        CB
                    </div>
                    <div>
                        <span class="text-xl font-serif tracking-wider font-bold text-white uppercase block leading-none">
                            BERCO <span class="text-coffee-primary">CAFE</span>
                        </span>
                        <span class="text-[9px] font-mono tracking-widest text-amber-200/80 uppercase block mt-1">Specialty Coffee & Roastery</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center space-x-7 text-xs uppercase tracking-wider font-semibold text-stone-300">
                    <a href="{{ route('home') }}" class="text-white hover:text-amber-300 transition-colors">Beranda</a>
                    <a href="{{ route('menu.index') }}" class="hover:text-amber-300 transition-colors">Menu Specialty</a>
                    <a href="#favorit" class="hover:text-amber-300 transition-colors">Menu Favorit</a>
                    <a href="#cerita" class="hover:text-amber-300 transition-colors">Tentang Kami</a>
                    <a href="#kontak" class="hover:text-amber-300 transition-colors">Lokasi</a>
                </div>
            </div>

            <div class="flex items-center space-x-3.5">
                <div class="hidden sm:inline-flex items-center space-x-2 px-3 py-1 rounded-full border border-white/15 bg-white/5 text-[11px] font-mono text-stone-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span id="navStatusText">BUKA 10:00 - 22:30 WIB</span>
                </div>

                <a href="{{ route('cart.index') }}" class="inline-flex items-center space-x-2 text-xs font-mono text-white hover:text-amber-300 border border-white/20 px-3.5 py-2 rounded-md hover:bg-white/10 transition-colors bg-white/5">
                    <i class="fas fa-bag-shopping text-coffee-primary"></i>
                    <span>Keranjang</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="text-xs uppercase tracking-wider font-bold bg-coffee-primary hover:bg-coffee-primary-dark text-white px-5 py-2 rounded-md transition-colors shadow-sm">
                        Masuk
                    </a>
                @endguest
                @auth
                    <a href="{{ Auth::user()->isAdmin() || Auth::user()->isStaff() ? route('control.dashboard') : route('menu.index') }}" class="text-xs font-mono font-semibold bg-white text-coffee-900 px-4 py-2 rounded-md hover:bg-stone-100 transition-colors shadow-sm inline-flex items-center space-x-2">
                        <i class="fas fa-user-circle text-coffee-primary"></i>
                        <span>{{ Str::limit(Auth::user()->name, 12) }}</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- 2. HERO SECTION (ATMOSPHERIC DARK ROASTERY) --}}
    <section class="relative min-h-[80vh] flex items-center justify-center text-center px-6 py-20 bg-coffee-900 overflow-hidden">
        {{-- Background Image & Overlay --}}
        <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1920&auto=format&fit=crop" 
             alt="Berco Cafe Ambiance" 
             class="absolute inset-0 w-full h-full object-cover opacity-35 filter brightness-75">
        <div class="absolute inset-0 bg-gradient-to-b from-coffee-900/85 via-coffee-900/70 to-coffee-900/95"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center space-x-2 border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 rounded-full text-xs font-mono text-amber-300">
                <i class="fas fa-mug-hot text-coffee-primary"></i>
                <span>ARTISAN COFFEE & MODERN BITES • BANYUWANGI</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold tracking-tight text-white leading-tight">
                Sensasi Kopi Terbaik di <span class="text-coffee-primary">Berco Cafe</span>
            </h1>

            <p class="text-base sm:text-lg text-stone-200 font-normal leading-relaxed max-w-2xl mx-auto">
                Nikmati perpaduan biji kopi specialty pilihan Tanah Blambangan, hidangan lezat berkelas, dan atmosfer hangat yang nyaman untuk setiap momen Anda.
            </p>

            <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('menu.index') }}" class="text-sm font-semibold bg-coffee-primary hover:bg-coffee-primary-dark text-white px-7 py-3 rounded-md transition-colors shadow-md inline-flex items-center space-x-2">
                    <i class="fas fa-mug-hot text-xs"></i>
                    <span>Pesan Menu Sekarang</span>
                </a>
                <a href="#favorit" class="text-sm font-semibold border border-white/30 hover:border-white text-white hover:bg-white/10 px-7 py-3 rounded-md transition-colors backdrop-blur-xs">
                    Lihat Menu Favorit
                </a>
            </div>
        </div>
    </section>

    {{-- 3. FAVORITE MENU CONTINUOUS SLIDER (GESER KE KIRI DENGAN EFEK TIMBUL & UKURAN LEBIH KOMPAK) --}}
    <section id="favorit" class="py-16 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-coffee-border pb-5 gap-4">
                <div>
                    <span class="text-xs font-mono text-coffee-primary uppercase tracking-widest font-semibold">Paling Diminati</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-coffee-text mt-0.5">Menu Favorit Pilihan Berco</h2>
                    <p class="text-xs text-coffee-muted mt-1">Arahkan kursor untuk menahan animasi dan melihat detail menu.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="btnSlidePrev" class="w-8 h-8 rounded-full border border-coffee-border bg-white text-coffee-text hover:bg-coffee-primary hover:text-white hover:border-coffee-primary transition-colors flex items-center justify-center text-xs" title="Geser Kiri">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="btnSlideNext" class="w-8 h-8 rounded-full border border-coffee-border bg-white text-coffee-text hover:bg-coffee-primary hover:text-white hover:border-coffee-primary transition-colors flex items-center justify-center text-xs" title="Geser Kanan">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <a href="{{ route('menu.index') }}" class="text-xs font-mono text-coffee-primary hover:underline font-semibold ml-2">
                        Semua Menu →
                    </a>
                </div>
            </div>
        </div>

        @php
            $demoFavorites = [
                ['name' => 'Kopi Susu Gula Aren Berco', 'cat' => 'Signature Coffee', 'price' => 18000, 'img' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop', 'badge' => 'Best Seller', 'desc' => 'Espresso blend pilihan dengan susu segar dan aren murni.'],
                ['name' => 'Caramel Macchiato Gold', 'cat' => 'Espresso Based', 'price' => 24000, 'img' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?q=80&w=600&auto=format&fit=crop', 'badge' => 'Favorit', 'desc' => 'Steamed milk, double shot espresso, dan drizzle karamel gurih.'],
                ['name' => 'Matcha Latte Creamy', 'cat' => 'Non Coffee', 'price' => 22000, 'img' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?q=80&w=600&auto=format&fit=crop', 'badge' => 'Top Pick', 'desc' => 'Matcha ceremonial Uji dengan susu segar lembut.'],
                ['name' => 'Croissant Butter Flaky', 'cat' => 'Bakery & Pastry', 'price' => 20000, 'img' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?q=80&w=600&auto=format&fit=crop', 'badge' => 'Fresh Daily', 'desc' => 'Pastry renyah berlapis dengan mentega Prancis aromatik.'],
                ['name' => 'Manual Brew Ijen V60', 'cat' => 'Filter Coffee', 'price' => 22000, 'img' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=600&auto=format&fit=crop', 'badge' => 'Single Origin', 'desc' => 'Seduhan pour-over aroma melati dan keasaman sitrus segar.'],
                ['name' => 'Beef Rice Bowl Teriyaki', 'cat' => 'Main Course', 'price' => 28000, 'img' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&auto=format&fit=crop', 'badge' => 'Chef Choice', 'desc' => 'Irisan daging sapi lembut saus teriyaki di atas nasi hangat.']
            ];
            $baseList = (!empty($favoriteMenus) && $favoriteMenus->count() > 0) ? $favoriteMenus : collect($demoFavorites);
            // Duplikat 2x untuk loop infinite yang mulus tanpa jeda
            $slidingList = $baseList->concat($baseList)->concat($baseList);
        @endphp

        {{-- Marquee Viewport --}}
        <div id="marqueeViewport" class="marquee-viewport-wrap w-full overflow-hidden py-4 cursor-grab active:cursor-grabbing">
            <div id="marqueeTrack" class="marquee-track-auto flex gap-5 px-6">
                @foreach($slidingList as $item)
                    @php
                        $isObj = is_object($item);
                        $name = $isObj ? ($item->nama_menu ?? $item['name']) : $item['name'];
                        $price = $isObj ? ($item->harga ?? $item['price']) : $item['price'];
                        $img = ($isObj && !empty($item->gambar_menu)) ? asset('storage/' . $item->gambar_menu) : ($isObj ? ($item['img'] ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop') : $item['img']);
                        $cat = $isObj ? ($item->kategori?->nama_kategori ?? 'Specialty') : $item['cat'];
                        $badge = $isObj ? ($item['badge'] ?? 'Specialty') : $item['badge'];
                        $desc = $isObj ? ($item->deskripsi ?? ($item['desc'] ?? 'Racikan menu barista dengan bahan baku premium.')) : $item['desc'];
                    @endphp

                    {{-- Compact Card (Width: 245px) with Pop-up / Timbul Hover Effect --}}
                    <div class="favorite-card-pop w-[245px] shrink-0 bg-white border border-coffee-border rounded-lg overflow-hidden flex flex-col justify-between shadow-xs select-none">
                        {{-- Thumbnail Compact (Height 135px) --}}
                        <div class="relative h-[135px] bg-stone-100 overflow-hidden">
                            <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover" loading="lazy">
                            <span class="absolute top-2 left-2 bg-white/95 border border-coffee-border px-1.5 py-0.2 rounded text-[9.5px] font-mono font-bold text-coffee-primary">
                                {{ $badge }}
                            </span>
                            <span class="absolute bottom-2 right-2 bg-coffee-900/90 text-white px-1.5 py-0.2 rounded text-[9.5px] font-mono font-medium">
                                {{ $cat }}
                            </span>
                        </div>

                        {{-- Body Compact --}}
                        <div class="p-3.5 flex-1 flex flex-col justify-between space-y-2.5">
                            <div>
                                <h3 class="text-xs font-bold text-coffee-text leading-snug line-clamp-1" title="{{ $name }}">{{ $name }}</h3>
                                <p class="text-[11px] text-coffee-muted mt-0.5 line-clamp-2 leading-relaxed">{{ $desc }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-coffee-border/50">
                                <span class="font-mono text-xs font-bold text-coffee-text">Rp {{ number_format($price, 0, ',', '.') }}</span>
                                <a href="{{ route('menu.index') }}" class="text-[11px] font-mono font-semibold bg-coffee-cream hover:bg-coffee-900 hover:text-white text-coffee-text px-2.5 py-1 rounded border border-coffee-border transition-colors">
                                    + Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. STORY & ABOUT SECTION --}}
    <section id="cerita" class="border-t border-coffee-border bg-white/70 py-20">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-5">
                <span class="text-xs font-mono text-coffee-primary uppercase tracking-widest font-semibold">Cerita Kami</span>
                <h2 class="text-3xl font-serif font-bold text-coffee-text leading-tight">Kecintaan Terhadap Kopi Lokal Specialty Sejak 2018</h2>
                <p class="text-sm text-coffee-muted leading-relaxed">
                    Berco Cafe hadir dari hasrat untuk mengangkat kekayaan biji kopi lokal Tanah Blambangan Banyuwangi menjadi seduhan berstandar specialty yang elegan dan dapat dinikmati oleh semua kalangan.
                </p>
                <div class="space-y-3 pt-2 text-xs font-semibold text-coffee-text">
                    <div class="flex items-center space-x-3">
                        <span class="w-5 h-5 rounded-full bg-coffee-primary/10 text-coffee-primary flex items-center justify-center font-mono text-xs">✓</span>
                        <span>100% Biji Kopi Specialty Berkualitas dari Petani Lokal Tanah Blambangan</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-5 h-5 rounded-full bg-coffee-primary/10 text-coffee-primary flex items-center justify-center font-mono text-xs">✓</span>
                        <span>Suasana Warm & Cozy dengan Fasilitas Nyaman untuk WFC & Nongkrong</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-5 h-5 rounded-full bg-coffee-primary/10 text-coffee-primary flex items-center justify-center font-mono text-xs">✓</span>
                        <span>Ragam Pastry, Makanan Berat, dan Minuman Non-Coffee Segar</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                <div class="rounded-lg overflow-hidden border border-coffee-border bg-white h-60 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=800" alt="Specialty Coffee Brewing" class="w-full h-full object-cover">
                </div>
                <div class="rounded-lg overflow-hidden border border-coffee-border bg-white h-60 shadow-sm mt-6">
                    <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=800" alt="Latte Art Berco" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- 5. LOCATION & CONTACT CARDS --}}
    <section id="kontak" class="border-t border-coffee-border py-16">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
            <div class="bg-white border border-coffee-border rounded-lg p-6 shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-lg bg-coffee-cream text-coffee-primary flex items-center justify-center mx-auto text-lg">
                    <i class="fas fa-location-dot"></i>
                </div>
                <h4 class="text-xs font-mono uppercase tracking-wider font-bold text-coffee-text">LOKASI CAFE</h4>
                <p class="text-xs text-coffee-muted">Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi</p>
            </div>

            <div class="bg-white border border-coffee-border rounded-lg p-6 shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-lg bg-coffee-cream text-coffee-primary flex items-center justify-center mx-auto text-lg">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="text-xs font-mono uppercase tracking-wider font-bold text-coffee-text">JAM LAYANAN</h4>
                <p class="text-xs text-coffee-muted">Setiap Hari<br><strong class="text-coffee-text font-mono">10:00 – 22:30 WIB</strong></p>
            </div>

            <div class="bg-white border border-coffee-border rounded-lg p-6 shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-lg bg-coffee-cream text-coffee-primary flex items-center justify-center mx-auto text-lg">
                    <i class="fas fa-phone"></i>
                </div>
                <h4 class="text-xs font-mono uppercase tracking-wider font-bold text-coffee-text">WHATSAPP</h4>
                <p class="text-xs font-mono text-coffee-text font-semibold">+62 821 4103 1234</p>
            </div>

            <div class="bg-white border border-coffee-border rounded-lg p-6 shadow-sm space-y-2">
                <div class="w-10 h-10 rounded-lg bg-coffee-cream text-coffee-primary flex items-center justify-center mx-auto text-lg">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4 class="text-xs font-mono uppercase tracking-wider font-bold text-coffee-text">EMAIL RESMI</h4>
                <p class="text-xs font-mono text-coffee-muted">bercocafe.bwi@gmail.com</p>
            </div>
        </div>
    </section>

    {{-- 6. FOOTER --}}
    <footer class="border-t border-white/10 py-8 text-xs font-mono text-stone-400 bg-coffee-900 text-center">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <span>© 2026 BERCO CAFE BANYUWANGI. ALL RIGHTS RESERVED.</span>
            <span class="text-stone-500">CRAFTED FOR COFFEE LOVERS</span>
        </div>
    </footer>

    {{-- Real-time Status & Carousel Control Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Status Jam Buka
            const statusText = document.getElementById('navStatusText');
            if (statusText) {
                const now = new Date();
                const hour = now.getHours();
                const min = now.getMinutes();
                const isOpen = (hour > 10 && hour < 22) || (hour === 10 && min >= 0) || (hour === 22 && min <= 30);
                statusText.textContent = isOpen ? 'BUKA 10:00 - 22:30 WIB' : 'TUTUP (10:00 - 22:30)';
            }

            // Slider Navigation Buttons
            const track = document.getElementById('marqueeTrack');
            const prevBtn = document.getElementById('btnSlidePrev');
            const nextBtn = document.getElementById('btnSlideNext');

            if (prevBtn && nextBtn && track) {
                prevBtn.addEventListener('click', () => {
                    track.style.animationPlayState = 'paused';
                    track.scrollBy({ left: -260, behavior: 'smooth' });
                });

                nextBtn.addEventListener('click', () => {
                    track.style.animationPlayState = 'paused';
                    track.scrollBy({ left: 260, behavior: 'smooth' });
                });
            }
        });
    </script>
</body>
</html>
