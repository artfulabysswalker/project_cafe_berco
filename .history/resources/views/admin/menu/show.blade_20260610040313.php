@extends('dashboard')

@section('content')

<div class="max-w-2xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.menu') }}"
           class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Menu</span>
        </a>

        <h1 class="text-3xl font-bold text-amber-900">
            {{ $menu->nama_menu }}
        </h1>

        <p class="text-gray-600 mt-2">
            Menu details information
        </p>
    </div>

    <!-- Image Card -->
    @if($menu->foto)
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 border border-gray-100">
            <img src="{{ asset('storage/' . $menu->foto) }}"
                 class="w-full h-64 object-cover">
        </div>
    @endif

    <!-- Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 space-y-4 border border-gray-100">

        <!-- Price -->
        <div class="flex justify-between items-center">
            <span class="text-gray-500">Price</span>
            <span class="text-xl font-bold text-amber-700">
                Rp {{ number_format($menu->harga, 0, ',', '.') }}
            </span>
        </div>

        <!-- Rating -->
        <div class="flex justify-between items-center">
            <span class="text-gray-500">Rating</span>
            <span class="font-semibold text-yellow-500">
                ⭐ {{ $menu->rating ?? 0 }}
            </span>
        </div>

        <!-- Status -->
        <div class="flex justify-between items-center">
            <span class="text-gray-500">Status</span>

            @if($menu->status_tersedia)
                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                    Available
                </span>
            @else
                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-700">
                    Not Available
                </span>
            @endif
        </div>

        <!-- Description -->
        <div class="pt-4 border-t border-gray-100">
            <p class="text-gray-500 mb-2">Description</p>
            <p class="text-gray-700 leading-relaxed">
                {{ $menu->deskripsi }}
            </p>
        </div>

    </div>

    <!-- Actions -->
    <div class="flex gap-3 mt-6">

        <a href="{{ route('admin.menu.edit', $menu->id_menu) }}"
           class="flex-1 px-4 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg text-center transition">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>

        <a href="{{ route('admin.menu.discount', $menu->id_menu) }}"
           class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg text-center transition">
            <i class="fas fa-bolt mr-2"></i> Discount
        </a>

    </div>

</div>

@endsection