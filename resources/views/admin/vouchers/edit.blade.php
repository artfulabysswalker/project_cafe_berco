@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Voucher</h1>

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="code" class="block text-sm font-medium text-gray-900 mb-2">Kode Voucher *</label>
                <input type="text" name="code" id="code" value="{{ old('code', $voucher->code) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="DISKON50">
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-900 mb-2">Judul Voucher *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $voucher->title) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Diskon 50% untuk member setia">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Deskripsi lengkap tentang voucher ini">{{ old('description', $voucher->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-900 mb-2">Jenis Diskon *</label>
                    <select name="discount_type" id="discount_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="percentage" {{ old('discount_type', $voucher->discount_type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_type', $voucher->discount_type) === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                </div>

                <div>
                    <label for="discount_value" class="block text-sm font-medium text-gray-900 mb-2">Nilai Diskon *</label>
                    <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $voucher->discount_value) }}" 
                        step="0.01" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="50">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-900 mb-2">Kuantitas (Kosongkan untuk unlimited)</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $voucher->quantity) }}" 
                        min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="100">
                </div>

                <div>
                    <label for="minimum_purchase" class="block text-sm font-medium text-gray-900 mb-2">Minimum Pembelian</label>
                    <input type="number" name="minimum_purchase" id="minimum_purchase" value="{{ old('minimum_purchase', $voucher->minimum_purchase) }}" 
                        step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="50000">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-900 mb-2">Berlaku Dari *</label>
                    <input type="datetime-local" name="valid_from" id="valid_from" value="{{ old('valid_from', $voucher->valid_from->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-900 mb-2">Berlaku Hingga *</label>
                    <input type="datetime-local" name="valid_until" id="valid_until" value="{{ old('valid_until', $voucher->valid_until->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label for="voucher_type" class="block text-sm font-medium text-gray-900 mb-2">Tipe Voucher *</label>
                <select name="voucher_type" id="voucher_type" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="manual" {{ old('voucher_type', $voucher->voucher_type) === 'manual' ? 'selected' : '' }}>Manual - Dibagikan secara manual</option>
                    <option value="automatic" {{ old('voucher_type', $voucher->voucher_type) === 'automatic' ? 'selected' : '' }}>Otomatis - Dikirim ke customer tidak aktif</option>
                </select>
                <p class="text-sm text-gray-500 mt-2">Voucher otomatis akan dikirim otomatis ke customer yang tidak berkunjung >30 hari</p>
            </div>

            <div>
                <label for="is_active" class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-900">Aktif</span>
                </label>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.vouchers.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
