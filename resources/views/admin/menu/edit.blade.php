@extends('dashboard')

@section('content')

<div style="max-width:500px; margin:40px auto; background:white; padding:20px; border-radius:12px;">

    <h2>Edit Menu</h2>

    <form method="POST" action="{{ route('admin.menu.update', $menu->id_menu) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="text" name="nama_menu" value="{{ $menu->nama_menu }}" placeholder="Menu Name" style="width:100%; margin:10px 0;">

        <input type="number" name="harga" value="{{ $menu->harga }}" placeholder="Price" style="width:100%; margin:10px 0;">

        <!-- STATUS -->
        <label>
            <input type="checkbox" name="status_tersedia" value="1" {{ $menu->status_tersedia ? 'checked' : '' }}>
            Available
        </label>

        <br><br>

        <!-- IMAGE -->
        @if($menu->foto)
            <img src="{{ asset('storage/' . $menu->foto) }}" width="100">
        @endif

        <input type="file" name="foto" style="margin:10px 0;">

        <input type="number" step="0.1" name="rating" value="{{ $menu->rating }}" style="width:100%; margin:10px 0;">

        <textarea name="deskripsi" style="width:100%; margin:10px 0;">{{ $menu->deskripsi }}</textarea>

        <button type="submit" style="width:100%; padding:10px; background:#5d2e1a; color:white;">
            Update Menu
        </button>

    </form>

</div>

@endsection