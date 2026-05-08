@php
    $user = auth()->user();
@endphp

<x-layouts::app :title="__('Referral Dashboard')">
    <div class="mx-auto max-w-6xl space-y-8 py-8">
        <!-- Header Section -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Program Referral Kafe</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Ajak teman Anda dan dapatkan reward menarik!</p>
        </div>

        <!-- Referral Code Section -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- My Referral Code -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Kode Referral Saya</h2>
                
                @if($referralCode)
                    <div class="mt-4">
                        <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-950">
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Bagikan kode ini ke teman:</p>
                            <p class="mt-2 flex items-center justify-between">
                                <code class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $referralCode }}</code>
                                <button 
                                    onclick="navigator.clipboard.writeText('{{ $referralCode }}')"
                                    class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                                >
                                    Copy
                                </button>
                            </p>
                        </div>
                        <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-400">
                            📝 Bagikan dengan teman Anda melalui link: <br>
                            <code class="text-xs">{{ url('/') }}?ref={{ $referralCode }}</code>
                        </p>
                    </div>
                @else
                    <p class="mt-4 text-neutral-600 dark:text-neutral-400">Anda belum memiliki kode referral.</p>
                    <form action="{{ route('referral.generate') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">
                            Buat Kode Referral
                        </button>
                    </form>
                @endif
            </div>

            <!-- Balance & Stats -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Saldo & Statistik</h2>
                
                <div class="mt-4 space-y-4">
                    <div class="flex justify-between rounded-lg bg-green-50 p-4 dark:bg-green-950">
                        <span class="text-neutral-600 dark:text-neutral-400">Saldo Referral:</span>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($referralBalance, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-700">
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Referral Berhasil</p>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $completedReferrals }}</p>
                        </div>
                        <div class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-700">
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Pending</p>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                                {{ $referralsMade->where('status', 'pending')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referrals List -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Daftar Referral</h2>
            
            @if($referralsMade->isEmpty())
                <p class="mt-4 text-neutral-600 dark:text-neutral-400">Anda belum mereferensikan siapapun.</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-neutral-200 dark:border-neutral-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-neutral-900 dark:text-white">Nama Referral</th>
                                <th class="px-4 py-2 text-left text-neutral-900 dark:text-white">Email</th>
                                <th class="px-4 py-2 text-right text-neutral-900 dark:text-white">Reward</th>
                                <th class="px-4 py-2 text-center text-neutral-900 dark:text-white">Status</th>
                                <th class="px-4 py-2 text-left text-neutral-900 dark:text-white">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($referralsMade as $referral)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                    <td class="px-4 py-3 text-neutral-900 dark:text-white">
                                        {{ $referral->referee?->name ?? 'Menunggu' }}
                                    </td>
                                    <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                        {{ $referral->referee?->email ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-green-600 dark:text-green-400">
                                        Rp {{ number_format($referral->reward_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-3 py-1 text-xs font-medium
                                            @if($referral->status === 'completed')
                                                bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200
                                            @elseif($referral->status === 'pending')
                                                bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200
                                            @else
                                                bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200
                                            @endif
                                        ">
                                            {{ ucfirst($referral->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                        {{ $referral->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Apply Referral Code -->
        @if(!$user->referred_by)
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Gunakan Kode Referral</h2>
                
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                    Punya kode referral dari teman? Gunakan di sini untuk mendapatkan bonus!
                </p>
                
                <form action="{{ route('referral.apply') }}" method="POST" class="mt-4 flex gap-2">
                    @csrf
                    <input 
                        type="text" 
                        name="referral_code" 
                        placeholder="Masukkan kode referral"
                        class="flex-1 rounded-lg border border-neutral-200 bg-white px-4 py-2 text-neutral-900 placeholder-neutral-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder-neutral-500"
                    >
                    <button 
                        type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700"
                    >
                        Gunakan
                    </button>
                </form>
                
                @error('referral_code')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @else
            <div class="rounded-lg border border-l-4 border-l-green-600 border-neutral-200 bg-green-50 p-6 dark:border-neutral-700 dark:bg-green-950">
                <h3 class="font-semibold text-green-700 dark:text-green-300">✓ Anda Sudah Memiliki Referrer</h3>
                <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                    Terima kasih telah bergabung melalui referral dari {{ $user->referredBy->name }}
                </p>
            </div>
        @endif
    </div>

    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
        </script>
    @endif

    @if(session('error'))
        <script>
            alert('Terjadi kesalahan: {{ session('error') }}');
        </script>
    @endif
</x-layouts::app>
