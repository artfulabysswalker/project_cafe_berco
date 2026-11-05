
@extends('dashboard')

@section('page-title', 'Topping Management')
@section('breadcrumb', 'Toppings')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-stone-900 tracking-tight">Topping Management</h2>
            <p class="text-xs font-mono text-stone-500 mt-1">Manage topping prices, portions, and menu availability.</p>
        </div>

        <a href="{{ route('admin.toppings.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-stone-900 text-white px-4 py-2.5 rounded-lg text-xs font-mono hover:bg-stone-800 transition-colors">
            <i class="fas fa-plus"></i>
            ADD TOPPING
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="flex items-center gap-2 p-3 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-stone-200 rounded-lg p-4">
            <p class="text-[11px] font-mono uppercase tracking-wider text-stone-500">Total Toppings</p>
            <h3 class="text-2xl font-mono font-semibold text-stone-900 mt-2">{{ $toppings->total() }}</h3>
        </div>

        <div class="bg-white border border-stone-200 rounded-lg p-4">
            <p class="text-[11px] font-mono uppercase tracking-wider text-stone-500">Available on This Page</p>
            <h3 class="text-2xl font-mono font-semibold text-emerald-700 mt-2">
                {{ $toppings->getCollection()->where('is_available', true)->count() }}
            </h3>
        </div>

        <div class="bg-white border border-stone-200 rounded-lg p-4">
            <p class="text-[11px] font-mono uppercase tracking-wider text-stone-500">Unavailable on This Page</p>
            <h3 class="text-2xl font-mono font-semibold text-amber-700 mt-2">
                {{ $toppings->getCollection()->where('is_available', false)->count() }}
            </h3>
        </div>
    </div>

    {{-- Topping Table --}}
    <div class="bg-white border border-stone-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
            <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">
                Topping Inventory
            </h3>
            <span class="text-[11px] font-mono text-stone-400">{{ $toppings->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-[10px] uppercase">
                    <tr>
                        <th class="px-5 py-3">Topping</th>
                        <th class="px-5 py-3">Small</th>
                        <th class="px-5 py-3">Medium</th>
                        <th class="px-5 py-3">Large</th>
                        <th class="px-5 py-3">Menus</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-100">
                    @forelse($toppings as $topping)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3 min-w-44">
                                    @if($topping->image)
                                        <img src="{{ asset('storage/' . $topping->image) }}"
                                             alt="{{ $topping->name }}"
                                             class="w-11 h-11 rounded-lg object-cover border border-stone-200">
                                    @else
                                        <div class="w-11 h-11 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif

                                    <span class="font-semibold text-stone-900">{{ $topping->name }}</span>
                                </div>
                            </td>

                            @foreach(['small', 'medium', 'large'] as $size)
                                @php $item = $topping->sizes->firstWhere('size', $size); @endphp
                                <td class="px-5 py-3 text-stone-700 whitespace-nowrap">
                                    <div class="font-semibold">
                                        Rp {{ number_format($item?->price ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-stone-400 mt-1">
                                        {{ $item?->quantity ?? '-' }} {{ $item?->unit ?? '' }}
                                    </div>
                                </td>
                            @endforeach

                            <td class="px-5 py-3 text-stone-600">
                                {{ $topping->menus_count }}
                            </td>

                            <td class="px-5 py-3">
                                @if($topping->is_available)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded border border-emerald-200 bg-emerald-50 text-emerald-700 text-[10px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        AVAILABLE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded border border-amber-200 bg-amber-50 text-amber-700 text-[10px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        UNAVAILABLE
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.toppings.edit', $topping) }}"
                                       class="px-2.5 py-1.5 rounded border border-stone-200 text-stone-700 hover:bg-stone-100 transition-colors">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form action="{{ route('admin.toppings.availability', $topping) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                title="{{ $topping->is_available ? 'Disable' : 'Enable' }}"
                                                class="px-2.5 py-1.5 rounded border border-stone-200 text-stone-700 hover:bg-stone-100 transition-colors">
                                            <i class="fas {{ $topping->is_available ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.toppings.destroy', $topping) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this topping?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1.5 rounded border border-red-200 text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <i class="fas fa-layer-group text-3xl text-stone-300 mb-3"></i>
                                <p class="text-sm text-stone-600">No toppings found.</p>
                                <a href="{{ route('admin.toppings.create') }}"
                                   class="inline-block mt-2 text-xs font-mono text-[#C27835] hover:underline">
                                    Create your first topping →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($toppings->hasPages())
            <div class="px-5 py-4 border-t border-stone-200">
                {{ $toppings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
