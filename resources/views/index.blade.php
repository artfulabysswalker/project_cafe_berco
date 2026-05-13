<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berco Cafe - Landing Page</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- Flash Messages untuk Feedback Fitur -->
    @if(session('success'))
        <div class="alert-popup success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->has('daily'))
        <div class="alert-popup error">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first('daily') }}
        </div>
    @endif

    <header class="header">
        <div class="container header-container">
            <div class="logo-area">
                <i class="fas fa-coffee cup-icon"></i>
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
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="{{ route('menu.index') }}"><i class="fas fa-mug-hot"></i> Pesan Menu</a></li>
                    <li><a href="{{ route('cart.index') }}"><i class="fas fa-shopping-cart"></i> Keranjang</a></li>
                </ul>
            </nav>
            <div class="user-action">
                @guest
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </a>
                @endguest
                @auth
                    <div class="user-info-wrapper">
                        <div class="user-display" style="text-align: right; line-height: 1.2; color: white;">
                            <div style="font-weight: bold;"><i class="fas fa-user-circle"></i> {{ Auth::user()->name }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.8;"><i class="fas fa-star" style="color: #f1c40f;"></i> {{ Auth::user()->exp }} EXP</div>
                        </div>
                        
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn-admin-panel" title="Panel Admin" style="color: #f1c40f; margin: 0 10px; font-size: 1.2rem;">
                                <i class="fas fa-user-shield"></i>
                            </a>
                        @endif
                        
                        {{-- Route name di web.php adalah daily.claim, pastikan method POST --}}
                        <form method="POST" action="{{ route('daily.claim') }}">
                            @csrf
                            <button type="submit" class="btn-claim-daily" title="Ambil EXP Harian">
                                <i class="fas fa-gift"></i>
                            </button>
                        </form>

                        {{-- Tombol logout standar Laravel --}}
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="container hero-container">
            <div class="hero-logo-wrapper">
                <img src="https://via.placeholder.com/60/800000/FFFFFF?text=B" alt="Berco Logo" class="hero-logo">
            </div>
            <h2 class="hero-title">BERCO CAFE</h2>
            <p class="hero-subtitle">Nikmati kopi dan kue-kue terbaik dalam suasana yang nyaman.</p>
            <div class="hero-btns">
                <a href="{{ route('menu.index') }}" class="btn btn-primary">Pesan Sekarang</a>
                <a href="#" class="btn btn-secondary">Kunjungi Kami</a>
            </div>
        </div>
    </section>

    <section class="story-section">
        <div class="container story-container">
            <div class="story-content">
                <h2 class="story-title">Story Berco</h2>
                <div class="story-description">
                    <p>
                        <strong>Berawal dari Passion (2018)</strong> Berco dimulai dari sebuah garasi kecil dengan impian besar: menghadirkan kualitas kopi <em>specialty</em> yang bisa dinikmati semua kalangan di Banyuwangi.
                    </p>
                    <p>
                        Berdiri di jantung kota Banyuwangi, Berco Cafe lahir dari kecintaan kami terhadap kekayaan kopi lokal Tanah Blambangan. Kami bukan sekadar tempat minum kopi; kami adalah ruang temu bagi komunitas, kreativitas, dan aroma autentik yang menyatukan setiap cerita. Di sini, setiap seduhan adalah bentuk apresiasi kami terhadap petani lokal dan semangat eksplorasi anak muda Banyuwangi.
                    </p>
                </div>
            </div>
            <div class="story-images">
                <div class="story-img-card">
                    <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?q=80&w=600" alt="Proses Kopi">
                </div>
                <div class="story-img-card">
                    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=600" alt="Latte Art">
                </div>
            </div>
        </div>
    </section>

    <section class="gallery-section">
        <div class="container">
            <div class="gallery-header">
                <h2 class="gallery-title">Gallery</h2>
                <p class="gallery-subtitle">Sekilas tentang cafe kami</p>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=600" alt="Depan Cafe">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1596199011403-f9a8a705a610?q=80&w=600" alt="Minuman Matcha">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1512132411229-c30391241dd8?q=80&w=600" alt="Sosis Bakar">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1563245372-f21724e3856d?q=80&w=600" alt="Makanan">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?q=80&w=600" alt="Foto Makanan">
                </div>
            </div>
        </div>
    </section>

    <a href="#" class="floating-help">
        <i class="fas fa-question-circle"></i>
    </a>

    <section class="contact-section">
        <div class="container">
            <div class="contact-header">
                <h2 class="contact-title">Kunjungi Kami</h2>
                <p class="contact-subtitle">We'd love to serve you</p>
            </div>
            <div class="contact-grid">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt contact-icon"></i>
                    <h3>Lokasi</h3>
                    <p>Jl. SMA Negeri 1, Krajan, Purwoharjo, Kabupaten Banyuwangi</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-phone-alt contact-icon"></i>
                    <h3>Telepon</h3>
                    <p>62 821 4103 1234</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-envelope contact-icon"></i>
                    <h3>Email</h3>
                    <p>bercocafe.bwi@gmail.com</p>
                </div>
                <div class="contact-card">
                    <i class="fas fa-clock contact-icon"></i>
                    <h3>Jam Operasional</h3>
                    <p>16.00 - 22.00 WIB</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-brand">
                <div class="footer-logo">
                    <i class="fas fa-coffee"></i>
                    <span>Berco cafe</span>
                </div>
                <p>Menciptakan pengalaman kopi yang luar biasa sejak tahun 2018.</p>
            </div>

            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="{{ route('menu.index') }}">Menu</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Gallery</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-social">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BercoCafé. Semua hak dilindungi undang-undang.</p>
        </div>
    </footer>

    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
