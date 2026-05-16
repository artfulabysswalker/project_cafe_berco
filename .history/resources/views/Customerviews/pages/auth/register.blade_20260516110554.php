<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Berco Cafe</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#FEF3C7] antialiased text-[#422006]">

    <div class="flex flex-col items-center pt-8 px-4 pb-20">
        <div class="flex flex-col items-center mb-8 text-center">
            <div class="w-16 h-16 bg-[#78350F] rounded-full flex items-center justify-center mb-3 shadow-lg">
                <span class="text-2xl">☕</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-[#78350F]">BERCO CAFE</h1>
            <p class="text-zinc-600 text-sm mt-1">Sistem Pemesanan Online</p>
        </div>

        <div class="w-full max-w-lg bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl border border-orange-100/50">
            <h2 class="text-2xl font-bold mb-1">Selamat Datang</h2>
            <p class="text-zinc-500 mb-8 text-sm">Login atau daftar untuk mulai memesan</p>

            <div class="flex bg-zinc-100 rounded-full p-1.5 mb-8">
                <a href="{{ route('login') }}"
                    class="w-1/2 text-center text-zinc-500 py-2.5 rounded-full font-medium hover:text-zinc-800 transition">Login</a>
                <a href="#"
                    class="w-1/2 text-center bg-white text-[#78350F] py-2.5 rounded-full font-bold shadow-sm">Daftar</a>
            </div>

            <form action="{{ route('register.user') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1 ml-1">Nama Lengkap</label>
                    <input type="text" name="name"
                        class="w-full px-5 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="Nama Anda" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 ml-1">Username</label>
                    <input type="text" name="username"
                        class="w-full px-5 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="username" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 ml-1">Email</label>
                    <input type="email" name="email"
                        class="w-full px-5 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="email@example.com" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1 ml-1">Nomor Telepon</label>
                    <input type="text" name="phone"
                        class="w-full px-5 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 ml-1">Password</label>
                        <input type="password" name="password"
                            class="w-full px-5 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                            placeholder="••••••" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 ml-1">Konfirmasi</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-5 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                            placeholder="••••••" required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-[#78350F] text-white py-4 mt-4 rounded-2xl font-bold text-lg hover:bg-[#5D290B] transition-all shadow-lg shadow-orange-900/20">
                    Daftar
                </button>
            </form>

            <div class="relative my-8 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-100"></div>
                </div>
                <span class="relative px-4 bg-white text-zinc-400 text-xs uppercase tracking-widest">atau</span>
            </div>

            <a href="{{ route('home') }}"
                class="w-full flex items-center justify-center gap-3 border-2 border-zinc-100 py-4 rounded-2xl font-semibold text-zinc-700 hover:bg-zinc-50 transition">
                <span>👤</span> Lanjutkan sebagai Guest
            </a>
            <p class="text-center text-[10px] text-zinc-400 mt-3 italic text-pretty px-4">
                Mode guest memungkinkan Anda memesan tanpa akun (langsung ke halaman utama)
            </p>
        </div>

        <a href="{{ route('home') }}" class="mt-10 text-[#78350F] font-bold text-sm hover:underline tracking-wide">
            ← Kembali ke Beranda
        </a>
    </div>

</body>

</html>