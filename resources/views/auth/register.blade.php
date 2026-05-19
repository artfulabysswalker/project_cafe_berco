<x-guest-layout>
    <div class="w-full">
        <!-- Alert Notification - Real-time Shop Status -->
        <div id="shopStatusAlert" class="mb-4 p-3 bg-orange-100 border border-orange-300 rounded-lg text-center">
            <p class="text-sm text-orange-800">Kami sudah tutup. Buka kembali Besok pada Pukul 16.00 WIB</p>
        </div>

        <!-- Welcome Message -->
        <div class="mb-6 text-center">
            <h1 class="text-lg font-semibold text-gray-800">Selamat Datang</h1>
            <p class="text-sm text-gray-600">Login atau daftar untuk mulai memesan</p>
        </div>

        <!-- Tabs -->
        <div class="flex mb-6 bg-gray-200 rounded-full p-1">
            <a href="{{ route('login') }}" class="flex-1 py-2 px-4 rounded-full font-medium text-center text-gray-700 hover:bg-gray-300 transition">
                Login
            </a>
            <button type="button" class="flex-1 py-2 px-4 rounded-full font-medium bg-gray-400 text-white">
                Daftar
            </button>
        </div>

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Anda</label>
                <x-text-input 
                    id="name" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    placeholder="Nama Lengkap Anda"
                    class="w-full px-4 py-2 bg-gray-100 border-0 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:bg-white" 
                    required 
                    autofocus 
                    autocomplete="name" 
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <x-text-input 
                    id="email" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    placeholder="email@gmail.com"
                    class="w-full px-4 py-2 bg-gray-100 border-0 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:bg-white" 
                    required 
                    autocomplete="username" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <x-text-input 
                    id="phone" 
                    type="tel" 
                    name="phone" 
                    :value="old('phone')" 
                    placeholder="08121234567"
                    class="w-full px-4 py-2 bg-gray-100 border-0 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:bg-white" 
                />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <x-text-input 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="Passwordku123"
                    class="w-full px-4 py-2 bg-gray-100 border-0 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:bg-white" 
                    required 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <x-text-input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Passwordku123"
                    class="w-full px-4 py-2 bg-gray-100 border-0 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:bg-white" 
                    required 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Register Button -->
            <button type="submit" class="w-full py-3 bg-amber-900 hover:bg-amber-800 text-white font-semibold rounded-lg transition duration-200">
                Daftar
            </button>

            <!-- Guest Option -->
            <button type="button" onclick="window.location.href='/'" class="w-full py-3 border-2 border-gray-400 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                </svg>
                Lanjutkan sebagai Guest
            </button>
        </form>
    </div>
</x-guest-layout>
