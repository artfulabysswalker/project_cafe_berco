<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berco Cafe - Home</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#FFFBEB] antialiased text-[#422006]">
    
    <nav class="bg-[#78350F] text-white px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center">
            <span class="font-bold text-xl tracking-wider">BERCO</span>
            <span class="bg-[#22C55E] text-[10px] px-2 rounded-full font-bold uppercase">Buka</span>
        </a>
        
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-orange-200 transition">Beranda</a>
            <a href="#" class="hover:text-orange-200 transition">Pesan Menu</a>
            <a href="#" class="hover:text-orange-200 transition">Keranjang</a>
            <a href="{{ route('login') }}" class="bg-white text-[#78350F] px-6 py-2 rounded-lg font-bold hover:bg-orange-50 transition shadow-sm">
                Masuk
            </a>
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
                <button class="bg-[#C2410C] hover:bg-orange-800 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105">
                    Pesan Sekarang
                </button>
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