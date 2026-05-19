<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berco Cafe - Home</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fffaf3;
            color: #2d1606;
            overflow-x: hidden;
        }

        /* NAVBAR */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 18px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(120, 53, 15, 0.95);
            backdrop-filter: blur(12px);
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        nav a {
            text-decoration: none;
            color: white;
        }

        nav .hidden {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        nav .hidden a {
            font-size: 15px;
            font-weight: 500;
            position: relative;
            transition: 0.3s ease;
        }

        nav .hidden a::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0%;
            height: 2px;
            background: #fb923c;
            transition: 0.3s ease;
        }

        nav .hidden a:hover::after {
            width: 100%;
        }

        nav .hidden a:hover {
            color: #fdba74;
        }

        /* LOGIN BUTTON */
        nav .bg-white {
            background: white;
            color: #78350F !important;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        nav .bg-white:hover {
            transform: translateY(-2px);
            background: #fff1e3;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
        }

        /* HERO SECTION */
        .hero-container {
            height: 100vh;
            width: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 60px;
            overflow: hidden;
        }

        .hero-background {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.65));
            z-index: 5;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 900px;
            padding: 20px;
            animation: fadeInUp 1s ease-out;
        }

        .hero-title {
            font-size: clamp(2rem, 8vw, 6rem);
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 20px;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            color: white;
            animation: slideDown 1.2s ease-out;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 3vw, 1.3rem);
            color: #ffe7cc;
            line-height: 1.8;
            margin-bottom: 40px;
            animation: fadeInUp 1.4s ease-out;
        }

        /* BUTTONS */
        .cta-button {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 50%, #9a3412 100%);
            color: white;
            text-decoration: none;
            display: inline-block;
            padding: 18px 48px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            box-shadow: 0 12px 30px rgba(194, 65, 12, 0.4);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .cta-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .cta-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 18px 45px rgba(194, 65, 12, 0.6);
        }

        .cta-button:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* MAIN */
        main {
            width: 100%;
            max-width: 1300px;
            margin: auto;
            padding: 120px 40px;
        }

        /* STORY SECTION */
        #story {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            margin-bottom: 180px;
        }

        #story h2 {
            font-size: 4rem;
            margin-bottom: 30px;
            color: #78350F;
            position: relative;
            display: inline-block;
        }

        #story h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60%;
            height: 4px;
            background: linear-gradient(to right, #ea580c, #9a3412);
            border-radius: 2px;
        }

        #story p {
            font-size: 1.15rem;
            line-height: 2;
            color: #5b3417;
            margin-bottom: 15px;
        }

        #story img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 35px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #story img:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.25);
        }

        /* GALLERY SECTION */
        .gallery-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .gallery-header h2 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: #78350F;
        }

        .gallery-divider {
            height: 4px;
            width: 80px;
            background: linear-gradient(to right, #ea580c, #9a3412);
            margin: 0 auto;
            border-radius: 2px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            height: 350px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            transition: all 0.4s ease;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), filter 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08) rotate(1deg);
            filter: brightness(0.7);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        /* CONTACT CARD */
        .contact-section {
            background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%);
            border-radius: 48px;
            padding: 80px;
            margin-bottom: 100px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
        }

        .contact-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 50px;
            color: #78350F;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .contact-card {
            text-align: center;
            padding: 30px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #ffffff 0%, #fff7e6 100%);
        }

        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            line-height: 1;
        }

        .contact-card h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #78350F;
        }

        .contact-card p {
            font-size: 0.95rem;
            color: #6b5b4c;
            line-height: 1.6;
        }

        /* FOOTER */
        footer {
            background: linear-gradient(135deg, #78350F 0%, #5a2308 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-top: 80px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.1);
        }

        footer p {
            opacity: 0.85;
            letter-spacing: 1px;
            font-size: 0.95rem;
        }

        /* ANIMATIONS */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE MOBILE */
        @media(max-width: 992px) {
            nav {
                padding: 15px 20px;
                flex-wrap: wrap;
            }

            nav .hidden {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
                width: 100%;
                order: 3;
            }

            .hero-title {
                font-size: 2.5rem;
                letter-spacing: 2px;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            #story {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            #story h2 {
                font-size: 2.5rem;
            }

            .contact-section {
                padding: 40px 25px;
            }

            .contact-title {
                font-size: 2rem;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            main {
                padding: 60px 20px;
            }
        }

        @media(max-width: 768px) {
            nav {
                padding: 12px 15px;
            }

            nav .hidden {
                gap: 12px;
            }

            .cta-button {
                padding: 15px 32px;
                font-size: 0.95rem;
            }

            .gallery-item {
                height: 280px;
            }

            main {
                padding: 40px 15px;
            }

            .hero-content {
                padding: 10px;
            }
        }
    </style>
</head>


<body class="min-h-screen bg-[#FFFBEB] antialiased text-[#422006]">
    {{-- Success Message Alert --}}
    @if(session('success'))
        <div class="fixed top-20 right-6 z-[60] bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Error Message Alert --}}
    @if($errors->has('daily'))
        <div class="fixed top-20 right-6 z-[60] bg-orange-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <i class="fas fa-info-circle mr-2"></i> {{ $errors->first('daily') }}
        </div>
    @endif

    {{-- NAVBAR --}}
    <nav class="bg-[#78350F] text-white px-6 py-4 flex justify-between items-center fixed top-0 w-full z-50 shadow-lg">
        {{-- Logo & Status --}}
        <a href="{{ route('home') }}" class="flex flex-col items-center hover:opacity-90 transition">
            <span class="font-bold text-xl tracking-wider">🍵 BERCO</span>
            @php
                $hour = now()->format('H');
                $isOpen = ($hour >= 16 && $hour < 22);
            @endphp
            <span class="{{ $isOpen ? 'bg-[#22C55E]' : 'bg-red-500' }} text-[10px] px-2 rounded-full font-bold uppercase transition">
                {{ $isOpen ? '✓ Buka' : '✗ Tutup' }}
            </span>
        </a>

        {{-- Navigation Links --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-orange-200 transition">Beranda</a>
            <a href="{{ route('menu.index') }}" class="hover:text-orange-200 transition">Pesan Menu</a>
            <a href="{{ route('cart.index') }}" class="hover:text-orange-200 transition">
                <i class="fas fa-shopping-cart mr-2"></i>Keranjang
            </a>

            @guest
                <a href="{{ route('login') }}" class="bg-white text-[#78350F] px-6 py-2 rounded-lg font-bold hover:bg-orange-50 transition shadow-sm">
                    Masuk
                </a>
            @endguest

            @auth
                <div class="flex items-center gap-4 border-l border-orange-800 pl-4">
                    <div class="text-right">
                        <div class="font-bold text-xs">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-orange-200">{{ Auth::user()->exp ?? 0 }} EXP</div>
                    </div>

                    {{-- Daily Claim Button --}}
                    @if(!Auth::user()->last_daily_claim || !Auth::user()->last_daily_claim->isToday())
                        <form method="POST" action="{{ route('daily.claim') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-400 hover:text-yellow-300 transition animate-bounce" title="Klaim Bonus Harian!">
                                <i class="fas fa-gift text-lg"></i>
                            </button>
                        </form>
                    @endif

                    {{-- Admin Panel --}}
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" title="Panel Admin" class="text-yellow-400 hover:text-yellow-300 transition text-lg">
                            <i class="fas fa-user-shield"></i>
                        </a>
                    @endif

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-orange-200 transition" title="Keluar">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <div class="hero-container">
        <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1600" alt="Berco Cafe" class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">🍵 BERCO CAFE 🍵</h1>
            <p class="hero-subtitle">
                Nikmati kopi dan makanan terbaik dalam suasana yang nyaman di Banyuwangi bagian selatan.
            </p>
            <div class="flex flex-col md:flex-row gap-6 justify-center">
                <a href="{{ route('menu.index') }}" class="cta-button">
                    <i class="fas fa-shopping-bag mr-2"></i>Pesan Sekarang
                </a>
                <a href="#story" class="cta-button" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.2) 100%); border: 2px solid white;">
                    <i class="fas fa-arrow-down mr-2"></i>Pelajari Lebih
                </a>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main>
        {{-- STORY SECTION --}}
        <section id="story" class="py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-20 items-center mb-32">
                <div>
                    <h2>Cerita Berco</h2>
                    <p>
                        <span style="color: #C2410C; font-weight: 700;">Sejak 2018,</span> 
                        Berco Cafe lahir dari kecintaan kami terhadap kekayaan kopi lokal Tanah Blambangan.
                    </p>
                    <p>
                        Kami menghadirkan kualitas kopi specialty yang bisa dinikmati semua kalangan. Setiap seduhan adalah bentuk apresiasi kami terhadap petani lokal dan semangat eksplorasi anak muda Banyuwangi.
                    </p>
                    <p style="margin-top: 20px; font-size: 0.95rem; color: #78350F; line-height: 1.8;">
                        ✓ Kopi specialty berkualitas tinggi dari petani lokal Tanah Blambangan<br>
                        ✓ Suasana nyaman untuk bersantai dan berkumpul<br>
                        ✓ Menu makanan pilihan yang melengkapi pengalaman coffee break Anda
                    </p>
                </div>
                <div class="flex gap-6">
                    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500" alt="Coffee Specialty" class="w-1/2 h-[450px] object-cover rounded-[2rem] shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?w=500" alt="Coffee Interior" class="w-1/2 h-[450px] object-cover rounded-[2rem] shadow-2xl mt-16">
                </div>
            </div>
        </section>

        {{-- GALLERY SECTION --}}
        <section class="py-20 mb-32">
            <div class="gallery-header">
                <h2>Galeri Berco</h2>
                <div class="gallery-divider"></div>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=600" alt="Coffee Latte">
                    <div class="gallery-overlay">
                        <span style="color: white; font-weight: 700;">Specialty Coffee</span>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1559925393-8be0ec41b50d?w=600" alt="Coffee Setup">
                    <div class="gallery-overlay">
                        <span style="color: white; font-weight: 700;">Brewing Perfection</span>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600" alt="Cafe Ambiance">
                    <div class="gallery-overlay">
                        <span style="color: white; font-weight: 700;">Cozy Atmosphere</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- CONTACT SECTION --}}
        <section class="contact-section">
            <h2 class="contact-title">Kunjungi Kami</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">📍</div>
                    <h4>Lokasi</h4>
                    <p>Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">📞</div>
                    <h4>Telepon</h4>
                    <p>+62 821 4103 1234</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">✉️</div>
                    <h4>Email</h4>
                    <p>bercocafe.bwi@gmail.com</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">⏰</div>
                    <h4>Jam Buka</h4>
                    <p>16.00 - 22.00 WIB</p>
                </div>
            </div>
        </section>
    </main>

    {{-- FOOTER --}}
    <footer>
        <p><i class="fas fa-copyright mr-2"></i>2026 Berco Cafe Banyuwangi. All Rights Reserved.</p>
        <p style="margin-top: 10px; font-size: 0.9rem;">Crafted with ❤️ for Coffee Lovers</p>
    </footer>
</body>

</html>

