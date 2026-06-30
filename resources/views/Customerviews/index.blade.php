<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berco Cafe - Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- FLASH (kept but safe) -->
    @if(session('success'))
        <div class="alert-popup success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- HEADER (ALL ROUTES DISABLED) -->
    <header class="header">
        <div class="container header-container">

            <div class="logo-area">
                <div class="logo-text">
                    <h1 class="brand-name">BERCO</h1>

                    @php
                        $hour = now()->format('H');
                        $isOpen = ($hour >= 16 && $hour < 22);
                    @endphp

                    <span class="status-badge {{ $isOpen ? 'open' : 'closed' }}">
                        <i class="far fa-clock"></i> {{ $isOpen ? 'BUKA' : 'TUTUP' }}
                    </span>
                </div>
            </div>

            <!-- NAV DISABLED بالكامل -->
            <nav class="nav">
                <ul>
                    <li><span><i class="fas fa-home"></i> Beranda</span></li>
                </ul>
            </nav>

            <!-- USER AREA FULL OFF -->
            <div class="user-action">

                @auth
                    <div style="color:white;">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                    </div>

                    <div style="font-size:0.75rem; color:white; opacity:0.7;">
                        {{ Auth::user()->exp ?? 0 }} EXP
                    </div>
                @endauth

                @guest
                    <span style="color:white;">
                        <i class="fas fa-user"></i> Guest Mode
                    </span>
                @endguest

            </div>

        </div>
    </header>

    <!-- HERO (NO ROUTES) -->
    <section class="hero">
        <div class="hero-overlay"></div>

        <div class="container hero-container">
            <h2 class="hero-title">BERCO CAFE</h2>
            <p class="hero-subtitle">Semua fitur sementara dinonaktifkan.</p>

            <div class="hero-btns">
                <button class="btn btn-secondary" disabled>
                    Kunjungi Kami
                </button>
            </div>
        </div>
    </section>

    <!-- REST CONTENT STILL VISIBLE -->
    <section>
        <div class="container">
            <p style="text-align:center; padding:20px;">
                Website sedang dalam mode maintenance.
            </p>
        </div>
    </section>

    <!-- FOOTER (NO LINKS) -->
    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-brand">
                <i class="fas fa-coffee"></i>
                <span>Berco Cafe</span>
            </div>

            <p>&copy; 2026 Berco Cafe</p>

            <p style="font-size:0.8rem; opacity:0.7;">
                All navigation disabled
            </p>
        </div>
    </footer>

    <script src="{{ asset('script.js') }}"></script>

</body>
</html>