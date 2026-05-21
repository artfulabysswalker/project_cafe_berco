<x-guest-layout>

<div class="min-h-screen bg-[#F7F0D9] flex flex-col items-center px-5 py-8">

    <div class="text-center">
        <div class="w-24 h-24 rounded-full bg-[#8B3E00] flex items-center justify-center mx-auto">☕</div>
        <h1 class="text-4xl font-bold text-[#8B3E00] mt-4">BERCO CAFE</h1>
        <p class="text-gray-600 mt-2">Daftar untuk mulai memesan</p>
    </div>

    <div class="bg-gray-100 rounded-3xl border p-8 mt-5 w-full max-w-xl">
        <h2 class="text-2xl font-bold">Buat Akun</h2>

        <div class="mt-4">
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mt-6">
                <label class="font-semibold">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full mt-2 rounded-lg bg-gray-200 border-0 focus:ring-[#8B3E00]" required autofocus>
                @error('name') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label class="font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full mt-2 rounded-lg bg-gray-200 border-0 focus:ring-[#8B3E00]" required>
                @error('email') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label class="font-semibold">Password</label>
                <input type="password" name="password" class="w-full mt-2 rounded-lg bg-gray-200 border-0 focus:ring-[#8B3E00]" required>
                @error('password') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label class="font-semibold">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full mt-2 rounded-lg bg-gray-200 border-0 focus:ring-[#8B3E00]" required>
                @error('password_confirmation') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full mt-6 bg-[#8B3E00] hover:bg-[#723000] text-white py-3 rounded-lg font-bold">Daftar</button>
        </form>

        <div class="border-t mt-8 pt-6 text-center">
            <a href="{{ route('login') }}" class="text-[#8B3E00] font-semibold">Sudah punya akun? Login</a>
        </div>
    </div>

</div>

</x-guest-layout>
