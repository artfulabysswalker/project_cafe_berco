@extends('dashboard')

@section('content')

<div class="mb-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-amber-900">Menu Management</h1>
            <p class="text-gray-600 mt-1">Manage all coffee shop menu items</p>
        </div>
        <a href="{{ route('admin.menu.create') }}" 
           class="bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white px-6 py-2 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Add Menu</span>
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Menu Grid/List -->
    <div class="grid gap-4">
        @forelse($menus as $menu)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all overflow-hidden border-l-4 border-amber-500">
                <div class="p-5 flex justify-between items-center">
                    <div class="flex items-center gap-4 flex-1">
                        <!-- Menu Image -->
                        <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                            @if($menu->foto)
                                <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-100 to-amber-200">
                                    <i class="fas fa-coffee text-amber-600 text-xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Menu Info -->
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 text-lg">{{ $menu->nama_menu }}</h3>
                            <div class="flex gap-4 mt-2 text-sm">
                                <span class="text-gray-600">
                                    <span class="font-medium">Price:</span> Rp {{ number_format($menu->harga_menu, 0, ',', '.') }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      :class="$menu->status_menu ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                    {{ $menu->status_menu ? '✓ Available' : '✗ Unavailable' }}
                                </span>
                            </div>
                            @if($menu->deskripsi)
                                <p class="text-gray-600 text-sm mt-2 line-clamp-1">{{ $menu->deskripsi }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('admin.menu.show', $menu->id_menu) }}" 
                           class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.menu.edit', $menu->id_menu) }}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.menu.delete', $menu->id_menu) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                    title="Delete" onclick="return confirm('Are you sure you want to delete this menu?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">No menus found</p>
                <a href="{{ route('admin.menu.create') }}" class="inline-block mt-4 text-amber-600 hover:text-amber-700 font-medium">
                    Create your first menu →
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection