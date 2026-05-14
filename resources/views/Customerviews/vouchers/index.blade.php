@php
    $types = ['welcome', 'comeback', 'promotion', 'referral'];
@endphp

<x-layouts::app :title="__('Kelola Voucher - Admin')">
    <div class="mx-auto max-w-6xl space-y-8 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">💳 Kelola Voucher</h1>
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">Buat, edit, dan kelola semua voucher promo</p>
            </div>
            <a href="{{ route('admin.vouchers.create') }}" class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                ➕ Buat Voucher Baru
            </a>
        </div>

        <!-- Filters -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-neutral-900 dark:text-white">Tipe Voucher</label>
                    <select name="type" class="mt-1 w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">Semua</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-900 dark:text-white">Status</label>
                    <select name="status" class="mt-1 w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-neutral-600 px-4 py-2 font-semibold text-white hover:bg-neutral-700">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.vouchers.index') }}" class="rounded-lg bg-neutral-200 px-4 py-2 font-semibold text-neutral-900 hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white">
                        ↻ Reset
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950">
                <p class="text-sm font-semibold text-green-600 dark:text-green-400">✓ {{ session('success') }}</p>
            </div>
        @endif

        <!-- Vouchers Table -->
        <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="w-full bg-white dark:bg-neutral-900">
                <thead class="border-b border-neutral-200 dark:border-neutral-700">
                    <tr class="bg-neutral-50 dark:bg-neutral-800">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Kode & Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Diskon</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Tipe</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Valid Hingga</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $voucher->code }}</p>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $voucher->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($voucher->discount_percentage)
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $voucher->discount_percentage }}%</span>
                                    @if($voucher->max_discount)
                                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Max Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</p>
                                    @endif
                                @else
                                    <span class="font-bold text-neutral-900 dark:text-white">Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block rounded-full bg-neutral-200 px-3 py-1 text-xs font-semibold text-neutral-900 dark:bg-neutral-700 dark:text-white">
                                    {{ ucfirst($voucher->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ $voucher->valid_until->format('d M Y') }}</p>
                                @if($voucher->valid_until->isPast())
                                    <p class="text-xs text-red-600 dark:text-red-400">Sudah expired</p>
                                @elseif($voucher->valid_until->diffInDays(now()) <= 7)
                                    <p class="text-xs text-orange-600 dark:text-orange-400">{{ $voucher->valid_until->diffInDays(now()) }} hari lagi</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $voucher->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400' }}">
                                    {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="rounded bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400">
                                        Edit
                                    </a>
                                    <a href="{{ route('admin.voucher.distribute-form', $voucher) }}" class="rounded bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700 hover:bg-purple-200 dark:bg-purple-900 dark:text-purple-400">
                                        Distribusi
                                    </a>
                                    <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" class="inline" onsubmit="return confirm('Hapus voucher ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <p class="text-neutral-600 dark:text-neutral-400">Belum ada voucher</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $vouchers->links() }}
        </div>
    </div>
</x-layouts::app>
