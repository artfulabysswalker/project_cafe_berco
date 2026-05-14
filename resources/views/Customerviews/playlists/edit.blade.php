@extends('layouts.app')

@section('title', 'Edit Lagu - Cafe Berco')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">✏️ Edit Lagu</h1>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
        <form action="{{ route('admin.playlists.update', $playlist) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-sm font-semibold mb-2">Judul Lagu <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('title', $playlist->title) }}" placeholder="Masukkan judul lagu" required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="artist" class="block text-sm font-semibold mb-2">Artis <span class="text-red-500">*</span></label>
                <input type="text" id="artist" name="artist" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('artist', $playlist->artist) }}" placeholder="Masukkan nama artis" required>
                @error('artist')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-semibold mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Masukkan deskripsi lagu">{{ old('description', $playlist->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="spotify_url" class="block text-sm font-semibold mb-2">URL Spotify/YouTube</label>
                <input type="url" id="spotify_url" name="spotify_url" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('spotify_url', $playlist->spotify_url) }}" placeholder="https://spotify.com/track/...">
                @error('spotify_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image_url" class="block text-sm font-semibold mb-2">URL Cover Art</label>
                <input type="url" id="image_url" name="image_url" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('image_url', $playlist->image_url) }}" placeholder="https://...">
                @error('image_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-semibold mb-2">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" {{ old('status', $playlist->status) === 'active' ? 'selected' : '' }}>🟢 Aktif</option>
                    <option value="inactive" {{ old('status', $playlist->status) === 'inactive' ? 'selected' : '' }}>🟡 Tidak Aktif</option>
                    <option value="completed" {{ old('status', $playlist->status) === 'completed' ? 'selected' : '' }}>✅ Selesai Diputar</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition font-semibold">
                    💾 Update Lagu
                </button>
                <a href="{{ route('playlists.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
