<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FBFBFA]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Menu - Berco Cafe')</title>
    
    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Instrument Serif"', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        canvas: '#FBFBFA',
                        surface: '#FFFFFF',
                        border: '#E7E5E4',
                        ink: '#1C1917',
                        'ink-muted': '#78716C',
                        terracotta: '#C27835',
                        'terracotta-dark': '#9A3412',
                    }
                }
            }
        }
    </script>
    @yield('styles')
</head>
<body class="min-h-full flex flex-col font-sans bg-canvas text-ink antialiased selection:bg-stone-200">

    {{-- NAVBAR --}}
    <header class="sticky top-0 z-40 bg-canvas/95 backdrop-blur-xs border-b border-border">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                    <span class="w-7 h-7 rounded bg-ink text-white flex items-center justify-center font-mono font-bold text-xs">CB</span>
                    <span class="text-lg font-serif tracking-wide text-ink font-semibold uppercase">
                        Berco<span class="text-terracotta">.</span>
                    </span>
                </a>

                <nav class="hidden md:flex items-center space-x-5 text-xs uppercase tracking-wider font-medium text-ink-muted">
                    <a href="{{ route('home') }}" class="hover:text-ink transition-colors {{ request()->routeIs('home') ? 'text-ink font-semibold' : '' }}">Beranda</a>
                    <a href="{{ route('menu.index') }}" class="hover:text-ink transition-colors {{ request()->routeIs('menu*') ? 'text-ink font-semibold' : '' }}">Menu</a>
                    <a href="{{ route('redeem.index') }}" class="hover:text-ink transition-colors {{ request()->routeIs('redeem*') ? 'text-ink font-semibold' : '' }}">Tukar EXP</a>
                    <a href="{{ route('daily.quest') }}" class="hover:text-ink transition-colors {{ request()->routeIs('daily.quest*') || request()->routeIs('achievements*') ? 'text-ink font-semibold' : '' }}">Quest</a>
                    <a href="{{ route('playlists.index') }}" class="hover:text-ink transition-colors {{ request()->routeIs('playlists*') ? 'text-ink font-semibold' : '' }}">Playlist</a>
                    @auth
                        <a href="{{ route('order.history') }}" class="hover:text-ink transition-colors {{ request()->routeIs('order.history*') ? 'text-ink font-semibold' : '' }}">Pesanan</a>
                    @endauth
                </nav>
            </div>

            <div class="flex items-center space-x-3">
                {{-- Cart Button (Always Available) --}}
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center space-x-1.5 text-xs font-mono text-ink border border-border px-3 py-1.5 rounded-md hover:bg-stone-100 transition-colors bg-white shadow-2xs">
                    <i class="fas fa-bag-shopping text-terracotta"></i>
                    <span class="hidden sm:inline">Keranjang</span>
                    <span class="ml-1 px-1.5 py-0.2 bg-terracotta text-white rounded text-[10px] font-mono font-bold" id="cart-badge" style="display: none;">0</span>
                </a>

                @auth
                    @if(!Auth::user()->is_guest)
                        <a href="{{ route('redeem.index') }}" class="hidden sm:inline-flex items-center space-x-1 text-xs font-mono bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1.5 rounded-md">
                            <i class="fas fa-star text-amber-500"></i>
                            <span>{{ number_format(Auth::user()->exp ?? 0) }} EXP</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="text-xs font-mono text-ink-muted hover:text-ink border border-border px-2.5 py-1.5 rounded-md hover:bg-stone-100 transition-colors bg-white">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xs uppercase tracking-wider font-semibold bg-ink text-white px-3.5 py-1.5 rounded-md hover:bg-stone-800 transition-colors shadow-2xs">
                            Masuk
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-xs uppercase tracking-wider font-semibold bg-ink text-white px-3.5 py-1.5 rounded-md hover:bg-stone-800 transition-colors shadow-2xs">
                        Masuk
                    </a>
                @endauth

                {{-- Mobile Menu Trigger --}}
                <button class="md:hidden p-1.5 text-ink-muted border border-border rounded-md hover:bg-stone-100" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Nav Dropdown --}}
        <div id="siteNav" class="hidden md:hidden border-t border-border px-6 py-3 space-y-2 text-xs font-mono bg-white">
            <a href="{{ route('home') }}" class="block py-1 text-ink-muted hover:text-ink">Beranda</a>
            <a href="{{ route('menu.index') }}" class="block py-1 text-ink-muted hover:text-ink">Menu</a>
            <a href="{{ route('cart.index') }}" class="block py-1 text-ink-muted hover:text-ink">Keranjang Belanja</a>
            <a href="{{ route('redeem.index') }}" class="block py-1 text-ink-muted hover:text-ink">Tukar EXP</a>
            <a href="{{ route('daily.quest') }}" class="block py-1 text-ink-muted hover:text-ink">Quest</a>
            <a href="{{ route('playlists.index') }}" class="block py-1 text-ink-muted hover:text-ink">Playlist</a>
            @auth
                <a href="{{ route('order.history') }}" class="block py-1 text-ink-muted hover:text-ink">Pesanan</a>
            @endauth
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
        @yield('content')
    </main>

    {{-- GLOBAL FLOATING TOAST NOTIFICATION CONTAINER --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>

    {{-- FOOTER --}}
    <footer class="border-t border-border py-8 text-xs font-mono text-ink-muted bg-white text-center mt-auto">
        <p>&copy; 2026 BERCO CAFE BANYUWANGI. ALL RIGHTS RESERVED.</p>
    </footer>

    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('siteNav');
            if (nav) {
                nav.classList.toggle('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartBadge();
        });

        function updateCartBadge() {
            fetch('{{ route('cart.count') }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cart-badge');
                    if (badge && data && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-flex';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(() => {});
        }

        // Global Toast Notification
        function showToast(message, type = 'success', actionUrl = '{{ route('cart.index') }}', actionText = 'Lihat Keranjang') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto transform transition-all duration-300 ease-out translate-y-4 opacity-0 bg-ink text-white px-4 py-3 rounded-xl shadow-xl flex items-center justify-between space-x-3 border border-stone-700 text-xs`;

            const iconClass = type === 'success' ? 'fa-check-circle text-emerald-400' : (type === 'error' ? 'fa-triangle-exclamation text-red-400' : 'fa-info-circle text-amber-400');

            toast.innerHTML = `
                <div class="flex items-center space-x-2.5 flex-1 mr-2">
                    <i class="fas ${iconClass} text-sm"></i>
                    <span class="leading-snug font-sans font-medium">${message}</span>
                </div>
                ${actionUrl ? `
                    <a href="${actionUrl}" class="px-2.5 py-1 bg-terracotta hover:bg-terracotta-dark text-white rounded font-mono text-[11px] font-semibold whitespace-nowrap transition-colors">
                        ${actionText}
                    </a>
                ` : ''}
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            // Animate out & remove after 3.5s
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    </script>
    @yield('scripts')
</body>
</html>
