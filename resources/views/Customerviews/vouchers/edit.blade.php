<x-layouts::app :title="__('Edit Voucher - Admin')">
    <div class="mx-auto max-w-2xl py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">✏️ Edit Voucher</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ $voucher->name }}</p>
        </div>

        <form method="POST" action="{{ route('admin.vouchers.update', $voucher) }}" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
            @csrf
            @method('PUT')

            <!-- Kode Voucher -->
            <div>
                <label for="code" class="block text-sm font-medium text-neutral-900 dark:text-white">Kode Voucher *</label>
                <input type="text" id="code" name="code" value="{{ old('code', $voucher->code) }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('code') border-red-500 @enderror" required>
                @error('code')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Voucher -->
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-900 dark:text-white">Nama Voucher *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $voucher->name) }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-neutral-900 dark:text-white">Deskripsi</label>
                <textarea id="description" name="description" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('description') border-red-500 @enderror" rows="3">{{ old('description', $voucher->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipe Voucher -->
            <div>
                <label for="type" class="block text-sm font-medium text-neutral-900 dark:text-white">Tipe Voucher *</label>
                <select id="type" name="type" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('type') border-red-500 @enderror" required>
                    <option value="welcome" {{ old('type', $voucher->type) === 'welcome' ? 'selected' : '' }}>Selamat Datang</option>
                    <option value="comeback" {{ old('type', $voucher->type) === 'comeback' ? 'selected' : '' }}>Comeback</option>
                    <option value="referral" {{ old('type', $voucher->type) === 'referral' ? 'selected' : '' }}>Referral</option>
                    <option value="promotion" {{ old('type', $voucher->type) === 'promotion' ? 'selected' : '' }}>Promosi</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Diskon -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="discount_percentage" class="block text-sm font-medium text-neutral-900 dark:text-white">Diskon %</label>
                    <input type="number" id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage', $voucher->discount_percentage) }}" min="0" max="100" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('discount_percentage') border-red-500 @enderror">
                    @error('discount_percentage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="discount_amount" class="block text-sm font-medium text-neutral-900 dark:text-white">Diskon (Rp)</label>
                    <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $voucher->discount_amount) }}" min="0" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('discount_amount') border-red-500 @enderror">
                    @error('discount_amount')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Max Discount & Min Purchase -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="max_discount" class="block text-sm font-medium text-neutral-900 dark:text-white">Max Diskon (Rp)</label>
                    <input type="number" id="max_discount" name="max_discount" value="{{ old('max_discount', $voucher->max_discount) }}" min="0" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('max_discount') border-red-500 @enderror">
                    @error('max_discount')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="min_purchase" class="block text-sm font-medium text-neutral-900 dark:text-white">Min. Pembelian (Rp)</label>
                    <input type="number" id="min_purchase" name="min_purchase" value="{{ old('min_purchase', $voucher->min_purchase) }}" min="0" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('min_purchase') border-red-500 @enderror">
                    @error('min_purchase')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Valid Period -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-neutral-900 dark:text-white">Berlaku Dari *</label>
                    <input type="date" id="valid_from" name="valid_from" value="{{ old('valid_from', $voucher->valid_from->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('valid_from') border-red-500 @enderror" required>
                    @error('valid_from')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-neutral-900 dark:text-white">Berlaku Hingga *</label>
                    <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until', $voucher->valid_until->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('valid_until') border-red-500 @enderror" required>
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Max Uses -->
            <div>
                <label for="max_uses" class="block text-sm font-medium text-neutral-900 dark:text-white">Max Penggunaan</label>
                <input type="number" id="max_uses" name="max_uses" value="{{ old('max_uses', $voucher->max_uses) }}" min="1" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('max_uses') border-red-500 @enderror">
                @error('max_uses')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="is_active" class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-neutral-300">
                    <span class="text-sm font-medium text-neutral-900 dark:text-white">Aktifkan Voucher</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    ✓ Simpan Perubahan
                </button>
                <a href="{{ route('admin.vouchers.index') }}" class="flex-1 rounded-lg border border-neutral-300 px-6 py-3 text-center font-semibold text-neutral-900 hover:bg-neutral-100 dark:border-neutral-600 dark:text-white dark:hover:bg-neutral-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts::app>
