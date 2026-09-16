<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Berco Cafe</title>

    {{-- Tailwind CSS & Alpine.js --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        [x-cloak] {
            display: none !important;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slideIn {
            animation: slideInUp 0.6s ease-out;
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#FEF3C7] via-[#FEF9E7] to-[#FDE68A] antialiased text-[#422006]">

    {{-- Background Decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 -right-32 w-64 h-64 bg-orange-200/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-yellow-200/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative flex flex-col items-center pt-8 md:pt-12 px-4 min-h-screen">
        {{-- Header Logo --}}
        <div class="animate-slideIn flex flex-col items-center mb-6 text-center">
            <a href="{{ route('home') }}" class="mb-3 inline-block hover:scale-105 transition">
                <div class="w-18 h-18 md:w-20 md:h-20 bg-gradient-to-br from-[#78350F] to-[#5a2308] rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition">
                    <span class="text-3xl">☕</span>
                </div>
            </a>
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-[#78350F]">BERCO CAFE</h1>
            <p class="text-zinc-600 mt-1 text-xs md:text-sm">Sistem Pemesanan Online & Loyalty Program</p>
        </div>

        {{-- Status Alert --}}
        <div class="w-full max-w-lg bg-gradient-to-r from-[#ECF0FF] to-[#F3E8FF] border-l-4 border-[#10B981] rounded-lg p-3.5 mb-6 text-center shadow-xs animate-slideIn">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-gift text-[#10B981] text-xs"></i>
                <p class="text-[#065F46] text-xs font-medium">
                    Daftar akun baru dan dapatkan <span class="font-bold">100 loyalty point gratis!</span>
                </p>
            </div>
        </div>

        {{-- Interactive Auth Card (Alpine.js State with 'register' active) --}}
        <div x-data="{ tab: 'register' }" 
             class="w-full max-w-lg bg-white p-6 md:p-8 rounded-3xl shadow-2xl border border-orange-100/50 animate-slideIn" style="animation-delay: 0.1s;">
            
            {{-- Tabs Switcher (Login vs Daftar) --}}
            <div class="flex gap-2 bg-zinc-100 rounded-full p-1.5 mb-6">
                <a href="{{ route('login') }}" 
                   @click.prevent="tab = 'login'" 
                   :class="tab === 'login' ? 'bg-white text-[#78350F] shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800 font-medium'"
                   class="w-1/2 text-center py-2.5 rounded-full transition-all flex items-center justify-center text-xs md:text-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <a href="{{ route('register') }}" 
                   @click.prevent="tab = 'register'" 
                   :class="tab === 'register' ? 'bg-white text-[#78350F] shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800 font-medium'"
                   class="w-1/2 text-center py-2.5 rounded-full transition-all flex items-center justify-center text-xs md:text-sm">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </a>
            </div>

            {{-- ======================================================== --}}
            {{-- TAB 1: FORM LOGIN                                        --}}
            {{-- ======================================================== --}}
            <div x-show="tab === 'login'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <h2 class="text-xl md:text-2xl font-bold mb-1 text-[#422006]">Selamat Datang Kembali</h2>
                <p class="text-zinc-500 mb-6 text-xs">Login dengan akun Anda untuk memesan kopi favorit</p>

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 ml-1 text-zinc-700">Email / Username</label>
                        <input type="text" name="email" required
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs"
                            placeholder="admin@bercocafe.com atau username">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5 ml-1">
                            <label class="text-xs font-semibold text-zinc-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-[11px] text-zinc-400 hover:text-orange-700">Lupa Password?</a>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-[#78350F] to-[#5a2308] text-white py-3.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-zinc-100 text-center">
                    <p class="text-zinc-600 text-xs">
                        Belum punya akun? 
                        <button type="button" @click="tab = 'register'" class="font-bold text-[#78350F] hover:text-orange-700 transition">
                            Daftar sekarang
                        </button>
                    </p>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- TAB 2: FORM DAFTAR / REGISTRASI                          --}}
            {{-- ======================================================== --}}
            <div x-show="tab === 'register'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <h2 class="text-xl md:text-2xl font-bold mb-1 text-[#422006]">Buat Akun Baru</h2>
                <p class="text-zinc-500 mb-6 text-xs">Bergabung untuk mengumpulkan poin reward & diskon loyalty!</p>

                {{-- Register Form --}}
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 ml-1 flex items-center gap-1.5 text-zinc-700">
                            <i class="fas fa-user text-[#78350F] text-xs"></i>
                            Nama Lengkap
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs"
                            placeholder="Masukkan nama lengkap Anda">
                        @error('name')
                            <p class="text-red-500 text-[11px] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 ml-1 flex items-center gap-1.5 text-zinc-700">
                            <i class="fas fa-envelope text-[#78350F] text-xs"></i>
                            Alamat Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="text-red-500 text-[11px] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor WhatsApp / HP --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 ml-1 flex items-center gap-1.5 text-zinc-700">
                            <i class="fab fa-whatsapp text-[#78350F] text-xs"></i>
                            Nomor WhatsApp / HP <span class="text-zinc-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs"
                            placeholder="081234567890">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 ml-1 flex items-center gap-1.5 text-zinc-700">
                            <i class="fas fa-lock text-[#78350F] text-xs"></i>
                            Password
                        </label>
                        <input type="password" name="password" required
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs"
                            placeholder="Minimal 6 karakter">
                        @error('password')
                            <p class="text-red-500 text-[11px] mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 ml-1 flex items-center gap-1.5 text-zinc-700">
                            <i class="fas fa-lock text-[#78350F] text-xs"></i>
                            Konfirmasi Password
                        </label>
                        <input type="password" name="password_confirmation" required
                            class="input-focus w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-orange-200 outline-none transition text-xs"
                            placeholder="Ulangi password Anda">
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-3.5 rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-green-900/20 transition-all transform hover:scale-[1.01] active:scale-98">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Akun Sekarang
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6 text-center">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-zinc-100"></div>
                    </div>
                    <span class="relative px-3 bg-white text-zinc-400 text-[10px] uppercase font-mono tracking-wider">atau</span>
                </div>

                {{-- Guest Login --}}
                <form method="POST" action="{{ route('guest.login') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 border border-zinc-200 py-3 rounded-xl font-medium text-xs text-zinc-700 hover:bg-zinc-50 transition">
                        <span>👤 Lanjutkan sebagai Guest</span>
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-zinc-100 text-center">
                    <p class="text-zinc-600 text-xs">
                        Sudah memiliki akun? 
                        <button type="button" @click="tab = 'login'" class="font-bold text-[#78350F] hover:text-orange-700 transition">
                            Login di sini
                        </button>
                    </p>
                </div>
            </div>

        </div>

        {{-- Back Button --}}
        <a href="{{ route('home') }}" class="mt-8 mb-6 inline-flex items-center gap-2 text-[#78350F] font-semibold text-xs hover:gap-3 transition">
            <i class="fas fa-arrow-left"></i>Kembali ke Beranda
        </a>

        {{-- Footer Info --}}
        <div class="text-center text-zinc-500 text-[11px] mt-auto pb-6 max-w-lg">
            <p>Dengan menggunakan layanan ini, Anda menyetujui <a href="#" class="text-orange-700 hover:underline">Syarat & Ketentuan</a> Cafe Berco</p>
        </div>
    </div>

</body>
</html>
