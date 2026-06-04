@extends('dashboard')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-receipt text-amber-600 mr-3"></i>Receipt Settings
        </h1>
        <p class="text-gray-600">Configure your café receipt information and preview</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.receipt.update') }}" enctype="multipart/form-data">
                @csrf

                <!-- Logo Section -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-image text-amber-600 mr-3"></i>Logo
                    </h2>

                    <div class="flex items-end gap-6">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Logo</label>
                            <div class="flex items-center gap-3">
                                <input type="file" name="logo" id="logo" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 text-sm">
                                <button type="button" onclick="document.getElementById('logo').click()" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition whitespace-nowrap">
                                    <i class="fas fa-upload mr-2"></i>Choose
                                </button>
                            </div>
                        </div>

                        @if($settings->logo)
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $settings->logo) }}" width="100" class="rounded-lg shadow-md">
                                <p class="text-xs text-gray-500 mt-2">Current Logo</p>
                            </div>
                        @else
                            <div class="text-center bg-gray-100 p-6 rounded-lg">
                                <i class="fas fa-image text-gray-400 text-3xl mb-2 block"></i>
                                <p class="text-sm text-gray-500">No logo</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Section -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-amber-600 mr-3"></i>Café Information
                    </h2>

                    <div class="space-y-4">
                        <!-- Cafe Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-coffee text-amber-600 mr-2"></i>Café Name
                            </label>
                            <input type="text" name="cafe_name" value="{{ $settings->cafe_name }}" placeholder="e.g., Cafe Barco" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-amber-600 mr-2"></i>Address
                            </label>
                            <input type="text" name="address" value="{{ $settings->address }}" placeholder="e.g., Jl. Kopi No. 123, Jakarta Pusat" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone text-amber-600 mr-2"></i>Phone Number
                            </label>
                            <input type="text" name="phone" value="{{ $settings->phone }}" placeholder="e.g., (021) 1234-5678" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                        </div>

                        <!-- Footer Message -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-message text-amber-600 mr-2"></i>Footer Message
                            </label>
                            <textarea name="footer_message" placeholder="Thank you for your visit!" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 resize-none">{{ $settings->footer_message }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- WiFi Section -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-wifi text-amber-600 mr-3"></i>WiFi Information
                    </h2>

                    <div class="space-y-4">
                        <!-- WiFi Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-wifi text-amber-600 mr-2"></i>WiFi Network Name
                            </label>
                            <input type="text" name="wifi_name" value="{{ $settings->wifi_name }}" placeholder="e.g., BERCO_CAFE" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                        </div>

                        <!-- WiFi Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-amber-600 mr-2"></i>WiFi Password
                            </label>
                            <input type="text" name="wifi_password" value="{{ $settings->wifi_password }}" placeholder="e.g., Kopi@2025" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-700 text-white font-bold py-3 px-6 rounded-lg hover:from-amber-700 hover:to-amber-800 transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="lg:col-span-1">
            <div class="sticky top-8">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-4 text-white">
                        <h3 class="font-bold text-center">
                            <i class="fas fa-eye mr-2"></i>Receipt Preview
                        </h3>
                    </div>

                    <!-- Thermal Receipt Preview -->
                    <div class="p-4">
                        <div class="w-full bg-white border border-dashed border-gray-300 p-4 font-mono text-xs leading-relaxed text-center">
                            <!-- Logo -->
                            @if($settings->logo)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $settings->logo) }}" width="60" class="mx-auto">
                                </div>
                            @endif

                            <!-- Café Info -->
                            <div class="mb-3 border-b border-dashed border-gray-400 pb-3">
                                <p class="font-bold">{{ $settings->cafe_name ?? 'Cafe Name' }}</p>
                                <p class="text-xs">{{ $settings->address ?? 'Address' }}</p>
                                <p class="text-xs">{{ $settings->phone ?? 'Phone' }}</p>
                            </div>

                            <!-- Sample Items -->
                            <div class="mb-3 border-b border-dashed border-gray-400 pb-3">
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Espresso x2</span>
                                    <span>40000</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span>Cappuccino x1</span>
                                    <span>35000</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="mb-3 border-b border-dashed border-gray-400 pb-3">
                                <div class="flex justify-between font-bold">
                                    <span>TOTAL</span>
                                    <span>75000</span>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="text-xs space-y-1">
                                <p>{{ $settings->footer_message ?? 'Thank you!' }}</p>
                                @if($settings->wifi_name)
                                    <p class="border-t border-dashed border-gray-400 pt-2 mt-2">
                                        WiFi: {{ $settings->wifi_name }}<br>
                                        Pass: {{ $settings->wifi_password ?? 'password' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Tip:</strong> Preview updates as you type!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    input[type="file"] {
        display: none;
    }
</style>
@endsection
