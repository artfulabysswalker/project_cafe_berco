@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Voucher</h1>
        <a href="{{ route('admin.vouchers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Buat Voucher Baru
        </a>
    </div>

    @if($message = Session::get('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ $message }}
        </div>
    @endif

    @if($vouchers->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berlaku Hingga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($vouchers as $voucher)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $voucher->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $voucher->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($voucher->discount_type === 'percentage')
                                    {{ $voucher->discount_value }}%
                                @else
                                    Rp{{ number_format($voucher->discount_value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $voucher->valid_until->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($voucher->voucher_type === 'automatic')
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $voucher->voucher_type === 'automatic' ? 'Otomatis' : 'Manual' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form action="{{ route('admin.vouchers.toggle-active', $voucher) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="
                                        @if($voucher->is_active)
                                            text-green-600
                                        @else
                                            text-red-600
                                        @endif
                                    ">
                                        {{ $voucher->is_active ? '✓ Aktif' : '✗ Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $vouchers->links() }}
        </div>
    @else
        <div class="bg-gray-50 rounded-lg p-12 text-center">
            <p class="text-gray-600 mb-4">Belum ada voucher yang dibuat.</p>
            <a href="{{ route('admin.vouchers.create') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                Buat voucher pertama →
            </a>
        </div>
    @endif
</div>
@endsection
