@extends('layouts.app')

@section('title', 'Tambah Lagu - Cafe Berco')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">➕ Tambah Lagu ke Playlist</h1>

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
        <form action="{{ route('admin.playlists.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-semibold mb-2">Judul Lagu <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('title') }}" placeholder="Masukkan judul lagu" required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="artist" class="block text-sm font-semibold mb-2">Artis <span class="text-red-500">*</span></label>
                <input type="text" id="artist" name="artist" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('artist') }}" placeholder="Masukkan nama artis" required>
                @error('artist')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-semibold mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Masukkan deskripsi lagu">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="spotify_url" class="block text-sm font-semibold mb-2">URL Spotify/YouTube</label>
                <input type="url" id="spotify_url" name="spotify_url" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('spotify_url') }}" placeholder="https://spotify.com/track/...">
                @error('spotify_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image_url" class="block text-sm font-semibold mb-2">URL Cover Art</label>
                <input type="url" id="image_url" name="image_url" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    value="{{ old('image_url') }}" placeholder="https://...">
                @error('image_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition font-semibold">
                    ✅ Simpan Lagu
                </button>
                <a href="{{ route('playlists.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
