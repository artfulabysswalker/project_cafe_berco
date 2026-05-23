<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Berco Cafe</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
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
        <div class="animate-slideIn flex flex-col items-center mb-8 text-center">
            <a href="{{ route('home') }}" class="mb-4 inline-block hover:scale-105 transition">
                <div class="w-20 h-20 bg-gradient-to-br from-[#78350F] to-[#5a2308] rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition">
                    <span class="text-3xl">☕</span>
                </div>
            </a>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-[#78350F]">BERCO CAFE</h1>
            <p class="text-zinc-600 mt-2 text-sm md:text-base">Sistem Pemesanan Online & Loyalty Program</p>
        </div>

        {{-- Status Alert --}}
        <div class="w-full max-w-lg bg-gradient-to-r from-[#FEF3C7] to-[#FFFBEC] border-l-4 border-[#F59E0B] rounded-lg p-4 mb-6 text-center shadow-sm animate-slideIn">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-clock text-[#F59E0B]"></i>
                <p class="text-[#92400E] text-sm font-medium">
                    Buka: <span class="font-bold">16.00 - 22.00 WIB</span>
                </p>
            </div>
        </div>

        {{-- Login Card --}}
        <div class="w-full max-w-lg bg-white p-8 md:p-10 rounded-3xl shadow-2xl border border-orange-100/50 animate-slideIn" style="animation-delay: 0.1s;">
            
            {{-- Tabs --}}
            <div class="flex gap-2 bg-zinc-100 rounded-full p-1.5 mb-8">
                <a href="#login" class="w-1/2 text-center bg-white text-[#78350F] py-2.5 rounded-full font-bold shadow-sm transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <a href="{{ route('register') }}" class="w-1/2 text-center text-zinc-500 py-2.5 rounded-full font-medium hover:text-zinc-800 transition">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </a>
            </div>

            <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang Kembali</h2>
            <p class="text-zinc-500 mb-8 text-sm">Login dengan akun Anda untuk memesan kopi favorit</p>

            {{-- Login Form --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Email Input --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 flex items-center gap-2">
                        <i class="fas fa-envelope text-[#78350F]"></i>
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="input-focus w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="masukkan@email.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div>
                    <div class="flex justify-between mb-2 ml-1">
                        <label class="text-sm font-semibold flex items-center gap-2">
                            <i class="fas fa-lock text-[#78350F]"></i>
                            Password
                        </label>
                        <a href="#" class="text-xs text-zinc-400 hover:text-orange-700 font-medium transition">
                            <i class="fas fa-question-circle mr-1"></i>Lupa?
                        </a>
                    </div>
                    <input type="password" name="password"
                        class="input-focus w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="••••••••" required>
                    @error('password')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-[#78350F] rounded" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 text-sm text-zinc-600 cursor-pointer hover:text-zinc-800">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl shadow-sm">
                        <div class="font-bold mb-3 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> Login Gagal
                        </div>
                        <ul class="space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="text-red-400 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Submit Button --}}
                <button type="submit" class="w-full bg-gradient-to-r from-[#78350F] to-[#5a2308] text-white py-4 rounded-2xl font-bold text-lg hover:shadow-lg hover:shadow-orange-900/30 transition-all transform hover:scale-[1.02] active:scale-95">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-8 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-100"></div>
                </div>
                <span class="relative px-4 bg-white text-zinc-400 text-xs uppercase tracking-widest font-medium">atau</span>
            </div>

            {{-- Guest Login --}}
            <form method="POST" action="{{ route('guest.login') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 border-2 border-zinc-200 py-4 rounded-2xl font-semibold text-zinc-700 hover:bg-zinc-50 hover:border-orange-300 transition">
                    <span class="text-lg">👤</span>
                    <span>Lanjutkan sebagai Guest</span>
                </button>
            </form>
            
            <p class="text-center text-[11px] text-zinc-400 mt-3 italic">
                <i class="fas fa-info-circle mr-1"></i>Mode guest memungkinkan Anda memesan tanpa akun (tidak dapat mengumpulkan poin)
            </p>

            {{-- Sign Up Link --}}
            <div class="mt-8 pt-6 border-t border-zinc-100 text-center">
                <p class="text-zinc-600 text-sm">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-bold text-[#78350F] hover:text-orange-700 transition">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>

        {{-- Back Button --}}
        <a href="{{ route('home') }}" class="mt-10 mb-6 inline-flex items-center gap-2 text-[#78350F] font-semibold text-sm hover:gap-3 transition">
            <i class="fas fa-arrow-left"></i>Kembali ke Beranda
        </a>

        {{-- Footer Info --}}
        <div class="text-center text-zinc-500 text-xs mt-auto pb-6 max-w-lg">
            <p>Dengan login, Anda menyetujui <a href="#" class="text-orange-700 hover:underline">Syarat & Ketentuan</a> kami</p>
        </div>
    </div>

</body>

</html>
