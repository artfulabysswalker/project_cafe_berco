@extends('dashboard')

@section('content')

<div class="max-w-2xl mx-auto">

    <!-- HEADER -->
    <div class="mb-6">

        <a href="{{ route('admin.menu') }}"
           class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Menu</span>
        </a>

        <h1 class="text-3xl font-bold text-amber-900">Set Discount</h1>

        <p class="text-gray-600 mt-2">
            Apply a temporary discount for
            <span class="font-semibold">{{ $menu->nama_menu }}</span>
        </p>

    </div>

    <!-- MENU INFO -->
    <div class="bg-white rounded-lg shadow-md p-5 mb-6 border border-gray-100">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500 text-sm">Original Price</p>

                <p class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                </p>

                @if($menu->discount_price && now()->between($menu->discount_start, $menu->discount_end))

                    <div class="mt-2">

                        <span style="text-decoration: line-through; color:#999; font-size:12px; margin-right:6px;">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </span>

                        <span style="color:#D4752C; font-weight:700;">
                            Rp {{ number_format($menu->discount_price, 0, ',', '.') }}
                        </span>

                        <div style="font-size:10px;color:#D4752C;margin-top:4px;">
                            🔥 Discount Active
                        </div>

                    </div>

                @endif

            </div>

            <div class="text-4xl">☕</div>

        </div>

    </div>

    <!-- ERRORS -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM -->
   <form method="POST"
      action="{{ route('admin.menu.discount', $menu->id_menu) }}">

        @csrf

        <!-- DISCOUNT PRICE -->
        <div class="mb-6">

            <label class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-tag text-red-500 mr-2"></i>
                Discount Price (Rp)
            </label>

            <input type="number"
                   name="discount_price"
                   placeholder="e.g. 15000"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                   required>

            <p class="text-sm text-gray-500 mt-1">
                Must be lower than original price
            </p>

        </div>

        <!-- DURATION -->
        <div class="mb-8">

            <label class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-clock text-blue-500 mr-2"></i>
                Duration
            </label>

            <select name="duration"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                    required>

                <option value="7">1 Week</option>
                <option value="14">2 Weeks</option>
                <option value="30">1 Month</option>

            </select>

            <p class="text-sm text-gray-500 mt-1">
                Discount will automatically expire after selected time
            </p>

        </div>

        <!-- BUTTONS (FIXED LAYOUT) -->
        <div class="flex gap-4">

            <button type="submit"
            <i class="fas fa-bolt"></i>
                <i class="fas fa-bolt"></i>
                <span>Apply Discount</span>
            </button>

            <a href="{{ route('admin.menu') }}"
               class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg text-center">
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection