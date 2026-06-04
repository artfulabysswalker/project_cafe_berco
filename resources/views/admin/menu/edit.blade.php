@extends('dashboard')

@section('content')

<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.menu') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium mb-4">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Menu</span>
        </a>
        <h1 class="text-3xl font-bold text-amber-900">Edit Menu Item</h1>
        <p class="text-gray-600 mt-2">Update menu information</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg">
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('admin.menu.update', $menu->id_menu) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Form -->
            <div class="space-y-6">
                <!-- Menu Name -->
                <div>
                    <label for="nama_menu" class="block text-sm font-semibold text-gray-800 mb-2">
                        <i class="fas fa-tag text-amber-600 mr-2"></i>Menu Name
                    </label>
                    <input type="text" id="nama_menu" name="nama_menu" value="{{ $menu->nama_menu }}" placeholder="Menu name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    @error('nama_menu')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="harga" class="block text-sm font-semibold text-gray-800 mb-2">
                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Price (Rp)
                    </label>
                    <input type="number" id="harga" name="harga" value="{{ $menu->harga }}" placeholder="Price"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    @error('harga')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rating -->
                <div>
                    <label for="rating" class="block text-sm font-semibold text-gray-800 mb-2">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>Rating
                    </label>
                    <input type="number" id="rating" name="rating" value="{{ $menu->rating }}" min="0" max="5" step="0.1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    @error('rating')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                    <input type="checkbox" id="status_tersedia" name="status_tersedia" value="1" 
                           {{ $menu->status_menu ? 'checked' : '' }} class="w-5 h-5 text-amber-600 rounded">
                    <label for="status_tersedia" class="flex-1 text-gray-700 font-medium cursor-pointer">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>Available for Order
                    </label>
                </div>
            </div>

            <!-- Right Column: Image -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    <i class="fas fa-image text-purple-600 mr-2"></i>Menu Image
                </label>
                
                <!-- Current Image -->
                @if($menu->foto)
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-2">Current Image:</p>
                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-48 object-cover rounded-lg">
                    </div>
                @endif

                <!-- Image Upload -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-amber-500 transition-colors cursor-pointer" id="dropZone">
                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <div id="imagePreview" style="display: none;" class="mb-4">
                        <img id="previewImg" src="" alt="Preview" class="max-h-40 mx-auto rounded-lg">
                    </div>
                    <div id="uploadPrompt">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600 font-medium text-sm">Click to change image</p>
                        <p class="text-gray-500 text-xs">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                @error('foto')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="mt-8 mb-8">
            <label for="deskripsi" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-align-left text-blue-600 mr-2"></i>Description
            </label>
            <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Describe your menu item..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">{{ $menu->deskripsi }}</textarea>
            @error('deskripsi')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit & Delete -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-save"></i>
                <span>Update Menu Item</span>
            </button>
            <a href="{{ route('admin.menu') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center">
                Cancel
            </a>
        </div>

        <!-- Delete Button -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="POST" action="{{ route('admin.menu.delete', $menu->id_menu) }}" onsubmit="return confirm('Are you sure you want to delete this menu item? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-6 py-2 bg-red-50 text-red-600 border border-red-200 font-semibold rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Delete Menu Item</span>
                </button>
            </form>
        </div>
    </form>
</div>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('foto');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-amber-500', 'bg-amber-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-amber-500', 'bg-amber-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-amber-500', 'bg-amber-50');
        fileInput.files = e.dataTransfer.files;
        previewImage({ target: { files: e.dataTransfer.files } });
    });

    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('uploadPrompt').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection