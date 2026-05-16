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

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{
    font-family:'Poppins',sans-serif;
    background:#fffaf3;
    color:#2d1606;
    overflow-x:hidden;
}

/* NAVBAR */
nav{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding:18px 60px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:rgba(120,53,15,0.9);
    backdrop-filter:blur(12px);
    z-index:1000;
    border-bottom:1px solid rgba(255,255,255,0.08);
}

nav a{
    text-decoration:none;
    color:white;
}

nav .hidden{
    display:flex;
    align-items:center;
    gap:35px;
}

nav .hidden a{
    font-size:15px;
    font-weight:500;
    position:relative;
    transition:0.3s;
}

nav .hidden a::after{
    content:'';
    position:absolute;
    bottom:-6px;
    left:0;
    width:0%;
    height:2px;
    background:#fb923c;
    transition:0.3s;
}

nav .hidden a:hover::after{
    width:100%;
}

nav .hidden a:hover{
    color:#fdba74;
}

/* LOGIN BUTTON */
nav .bg-white{
    background:white;
    color:#78350F !important;
    padding:12px 24px;
    border-radius:14px;
    font-weight:700;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

nav .bg-white:hover{
    transform:translateY(-2px);
    background:#fff1e3;
}

/* HERO SECTION */
.relative{
    position:relative;
}

.h-\[85vh\]{
    height:100vh;
}

.absolute{
    position:absolute;
}

.inset-0{
    inset:0;
}

.object-cover{
    object-fit:cover;
}

.z-0{
    z-index:0;
}

.z-10{
    z-index:10;
}

.z-20{
    z-index:20;
}

.bg-black\/60{
    background:linear-gradient(
        to bottom,
        rgba(0,0,0,0.55),
        rgba(0,0,0,0.65)
    );
}

.text-white{
    color:white;
}

.text-center{
    text-align:center;
}

.relative.z-20{
    max-width:900px;
    padding:20px;
}

h1{
    font-size:7rem;
    font-weight:800;
    letter-spacing:4px;
    margin-bottom:20px;
    text-shadow:0 10px 30px rgba(0,0,0,0.4);
    animation:fadeUp 1s ease;
}

.hero-text{
    font-size:1.3rem;
    color:#ffe7cc;
    line-height:1.8;
    margin-bottom:45px;
    animation:fadeUp 1.3s ease;
}

/* BUTTONS */
.bg-\[\#C2410C\]{
    background:linear-gradient(135deg,#ea580c,#9a3412);
    color:white;
    text-decoration:none;
    display:inline-block;
    box-shadow:0 12px 30px rgba(194,65,12,0.4);
}

.rounded-xl{
    border-radius:18px;
}

.px-10{
    padding-left:42px;
    padding-right:42px;
}

.py-4{
    padding-top:18px;
    padding-bottom:18px;
}

.font-bold{
    font-weight:700;
}

.text-lg{
    font-size:1.1rem;
}

.transition-all{
    transition:0.35s ease;
}

.hover\:scale-105:hover{
    transform:translateY(-5px) scale(1.05);
    box-shadow:0 18px 35px rgba(194,65,12,0.55);
}

/* MAIN */
main{
    width:100%;
    max-width:1300px;
    margin:auto;
    padding:120px 40px;
}

/* STORY SECTION */
#story{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:80px;
    align-items:center;
    margin-bottom:180px;
}

#story h2{
    font-size:4rem;
    margin-bottom:30px;
    color:#78350F;
}

#story p{
    font-size:1.15rem;
    line-height:2;
    color:#5b3417;
}

#story img{
    width:100%;
    height:500px;
    object-fit:cover;
    border-radius:35px;
    box-shadow:0 20px 40px rgba(0,0,0,0.15);
    transition:0.4s ease;
}

#story img:hover{
    transform:translateY(-10px) rotate(1deg);
}

/* GALLERY */
.grid{
    display:grid;
}

.md\:grid-cols-3{
    grid-template-columns:repeat(3,1fr);
}

.gap-8{
    gap:30px;
}

.grid img{
    width:100%;
    height:350px;
    object-fit:cover;
    border-radius:30px;
    transition:0.4s ease;
    box-shadow:0 15px 35px rgba(0,0,0,0.12);
}

.grid img:hover{
    transform:translateY(-8px) scale(1.02);
    filter:brightness(75%);
}

/* CONTACT CARD */
.bg-white{
    background:white;
}

.rounded-\[3rem\]{
    border-radius:48px;
}

.shadow-xl{
    box-shadow:0 20px 50px rgba(0,0,0,0.08);
}

.border{
    border:1px solid #fed7aa;
}

.p-12{
    padding:80px;
}

.text-4xl{
    font-size:2.5rem;
}

.text-center{
    text-align:center;
}

/* FOOTER */
footer{
    background:#78350F;
    color:white;
    padding:50px 20px;
    text-align:center;
    margin-top:120px;
}

footer p{
    opacity:0.75;
    letter-spacing:1px;
}

/* ANIMATION */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* MOBILE */
@media(max-width:992px){

    nav{
        padding:20px;
        flex-direction:column;
        gap:20px;
    }

    nav .hidden{
        flex-wrap:wrap;
        justify-content:center;
        gap:20px;
    }

    h1{
        font-size:4rem;
    }

    #story{
        grid-template-columns:1fr;
    }

    .md\:grid-cols-3{
        grid-template-columns:1fr;
    }

    .p-12{
        padding:40px 25px;
    }

    main{
        padding:80px 20px;
    }
}
</style>
</head>

<body class="min-h-screen bg-[#FFFBEB] antialiased text-[#422006]">
    @if(session('success'))
        <div class="fixed top-20 right-6 z-[60] bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->has('daily'))
        <div class="fixed top-20 right-6 z-[60] bg-orange-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <i class="fas fa-info-circle mr-2"></i> {{ $errors->first('daily') }}
        </div>
    @endif

    <nav class="bg-[#78350F] text-white px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center">
            <span class="font-bold text-xl tracking-wider">BERCO</span>
            @php
                $hour = now()->format('H');
                $isOpen = ($hour >= 16 && $hour < 22);
            @endphp
            <span class="{{ $isOpen ? 'bg-[#22C55E]' : 'bg-red-500' }} text-[10px] px-2 rounded-full font-bold uppercase">
                {{ $isOpen ? 'Buka' : 'Tutup' }}
            </span>
        </a>
        
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-orange-200 transition">Beranda</a>
            <a href="{{ route('menu.index') }}" class="hover:text-orange-200 transition">Pesan Menu</a>
<<<<<<< HEAD
            <a href="{{ route('cart.index') }}" class="hover:text-orange-200 transition">Keranjang</a>
            
            @guest
              <a href="{{ route('login') }}" class="bg-white text-[#78350F] px-6 py-2 rounded-lg font-bold hover:bg-orange-50 transition shadow-sm">
    Masuk
</a>
            @endguest

            @auth
                <div class="flex items-center gap-4 border-l border-orange-800 pl-4">
                    <div class="text-right">
                        <div class="font-bold text-xs">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-orange-200">{{ Auth::user()->exp }} EXP</div>
                    </div>

                    @if(!Auth::user()->last_daily_claim || !Auth::user()->last_daily_claim->isToday())
                        <form method="POST" action="{{ route('daily.claim') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-400 hover:text-yellow-300 transition animate-bounce" title="Klaim Bonus Harian!">
                                <i class="fas fa-gift text-lg"></i>
                            </button>
                        </form>
                    @endif

                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" title="Panel Admin" class="text-yellow-400 hover:text-yellow-300 transition text-lg">
                            <i class="fas fa-user-shield"></i>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-orange-200 transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            @endauth
=======
            <a href="#" class="hover:text-orange-200 transition">Keranjang</a>
            <a href="{{ route('login') }}" class="bg-white text-[#78350F] px-6 py-2 rounded-lg font-bold hover:bg-orange-50 transition shadow-sm">
                Masuk
            </a>
>>>>>>> c1f9b47b2b32ef16af6de90aff6579bb39bed917
        </div>
    </nav>

    <div class="relative h-[85vh] w-full flex items-center justify-center text-center overflow-hidden">
        <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1600" class="absolute inset-0 w-full h-full object-cover z-0" alt="Cafe">
        <div class="absolute inset-0 bg-black/60 z-10"></div>
        <div class="relative z-20 text-white px-4">
            <h1 class="text-6xl md:text-8xl font-bold tracking-tight mb-6">BERCO CAFE</h1>
            <p class="text-xl md:text-2xl font-light mb-10 max-w-3xl mx-auto text-orange-50">
                Nikmati kopi dan makanan terbaik dalam suasana yang nyaman di Banyuwangi bagian selatan.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
<<<<<<< HEAD
                <a href="{{ route('menu.index') }}" class="bg-[#C2410C] hover:bg-orange-800 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 no-underline inline-block">
=======
                <button onclick="window.location.href='{{ route('menu.index') }}'" class="bg-[#C2410C] hover:bg-orange-800 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105">
>>>>>>> c1f9b47b2b32ef16af6de90aff6579bb39bed917
                    Pesan Sekarang
                </a>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-24">
        <section id="story" class="grid grid-cols-1 md:grid-cols-2 gap-20 items-center mb-32">
            <div>
                <h2 class="text-5xl font-bold mb-8 italic">Story Berco</h2>
                <p class="text-xl mb-6 leading-relaxed">
                    <span class="text-[#C2410C] font-bold">Sejak 2018.</span> Berco Cafe lahir dari kecintaan kami terhadap kekayaan kopi lokal Tanah Blambangan.
                </p>
                <p class="text-zinc-600 text-lg leading-relaxed">
                    Kami menghadirkan kualitas kopi specialty yang bisa dinikmati semua kalangan. Setiap seduhan adalah bentuk apresiasi kami terhadap petani lokal dan semangat eksplorasi anak muda Banyuwangi.
                </p>
            </div>
            <div class="flex gap-6">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500" class="w-1/2 h-[450px] object-cover rounded-[2rem] shadow-2xl" alt="Coffee">
                <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?w=500" class="w-1/2 h-[450px] object-cover rounded-[2rem] shadow-2xl mt-16" alt="Coffee 2">
            </div>
        </section>

        <section class="mb-32">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold italic mb-4">Gallery</h2>
                <div class="h-1 w-20 bg-[#78350F] mx-auto"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=600" class="h-80 w-full object-cover rounded-3xl hover:brightness-75 transition">
                <img src="https://images.unsplash.com/photo-1559925393-8be0ec41b50d?w=600" class="h-80 w-full object-cover rounded-3xl hover:brightness-75 transition">
                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600" class="h-80 w-full object-cover rounded-3xl hover:brightness-75 transition">
            </div>
        </section>

        <section class="bg-white rounded-[3rem] p-12 md:p-20 shadow-xl border border-orange-100 mb-32">
            <h2 class="text-4xl font-bold text-center mb-16 italic">Kunjungi Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 text-center">
                <div><div class="text-4xl mb-4">📍</div><h4 class="font-bold mb-2">Lokasi</h4><p class="text-sm text-zinc-500">Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi</p></div>
                <div><div class="text-4xl mb-4">📞</div><h4 class="font-bold mb-2">Telepon</h4><p class="text-sm text-zinc-500">+62 821 4103 1234</p></div>
                <div><div class="text-4xl mb-4">✉️</div><h4 class="font-bold mb-2">Email</h4><p class="text-sm text-zinc-500">bercocafe.bwi@gmail.com</p></div>
                <div><div class="text-4xl mb-4">⏰</div><h4 class="font-bold mb-2">Jam Buka</h4><p class="text-sm text-zinc-500">16.00 - 22.00 WIB</p></div>
            </div>
        </section>
    </main>

    <footer class="bg-[#78350F] text-white py-12 text-center">
        <p class="opacity-70 text-sm">© 2026 Berco Cafe Banyuwangi. All Rights Reserved.</p>
    </footer>
</body>
</html>