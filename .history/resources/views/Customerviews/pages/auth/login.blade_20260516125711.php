<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Berco Cafe</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <div style="background:red; color:white; padding:20px; font-size:30px;">
        TEST
    </div>
</head>

<body class="min-h-screen bg-[#FEF3C7] antialiased text-[#422006]">

    <div class="flex flex-col items-center pt-12 px-4">
        <div class="flex flex-col items-center mb-8 text-center">
            <div class="w-20 h-20 bg-[#78350F] rounded-full flex items-center justify-center mb-3 shadow-lg">
                <span class="text-3xl">☕</span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-[#78350F]">BERCO CAFE</h1>
            <p class="text-zinc-600 mt-1">Sistem Pemesanan Online</p>
        </div>

        <div class="w-full max-w-lg bg-[#FFFBEC] border border-[#FDE68A] rounded-2xl p-4 mb-6 text-center shadow-sm">
            <p class="text-[#92400E] text-sm font-medium">Kami sudah tutup. Buka kembali besok pukul 16.00 WIB</p>
        </div>

        <div class="w-full max-w-lg bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl border border-orange-100/50">
            <h2 class="text-2xl font-bold mb-1">Selamat Datang</h2>
            <p class="text-zinc-500 mb-8 text-sm">Login atau daftar untuk mulai memesan</p>

            <div class="flex bg-zinc-100 rounded-full p-1.5 mb-8">
                <a href="#"
                    class="w-1/2 text-center bg-white text-[#78350F] py-2.5 rounded-full font-bold shadow-sm">Login</a>
                <a href="{{ route('testregister') }}"
                    class="w-1/2 text-center text-zinc-500 py-2.5 rounded-full font-medium hover:text-zinc-800">
                    Daftar
                </a>
            </div>

            <form action="{{ route('login.user') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1.5 ml-1">Email</label>
                    <input type="email" name="email"
                        class="w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="email@example.com" required>
                </div>

                <div>
                    <div class="flex justify-between mb-1.5 ml-1">
                        <label class="text-sm font-semibold">Password</label>
                        <a href="#" class="text-xs text-zinc-400 hover:text-orange-700">Lupa password?</a>
                    </div>
                    <input type="password" name="password"
                        class="w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="••••••" required>
                </div>

                <button type="submit"
                    class="w-full bg-[#78350F] text-white py-4 rounded-2xl font-bold text-lg hover:bg-[#5D290B] transition-all shadow-lg shadow-orange-900/20">
                    Login
                </button>


                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl shadow-sm">

                        <div class="font-bold mb-2">
                            ⚠️ Login Gagal
                        </div>

                        <ul class="space-y-1 text-sm list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif

            </form>

            <div class="relative my-10 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-100"></div>
                </div>
                <span class="relative px-4 bg-white text-zinc-400 text-xs uppercase tracking-widest">atau</span>
            </div>

            <button
                class="w-full flex items-center justify-center gap-3 border-2 border-zinc-100 py-4 rounded-2xl font-semibold text-zinc-700 hover:bg-zinc-50 transition">
                <span>👤</span> Lanjutkan sebagai Guest
            </button>
            <p class="text-center text-[10px] text-zinc-400 mt-3 italic">Mode guest memungkinkan Anda memesan tanpa akun
            </p>
        </div>

        <a href="{{ route('home') }}"
            class="mt-10 mb-20 text-[#78350F] font-bold text-sm hover:underline tracking-wide">
            ← Kembali ke Beranda
        </a>
    </div>

</body>

</html>