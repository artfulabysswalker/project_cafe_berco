@extends('dashboard')

@section('content')

<div style="max-width:500px; margin:40px auto; background:white; padding:20px; border-radius:12px;">

    <h2>{{ $menu->nama_menu }}</h2>

    @if($menu->foto)
        <img src="{{ asset('storage/' . $menu->foto) }}" style="width:100%; border-radius:10px; margin:10px 0;">
    @endif

    <p><b>Price:</b> {{ $menu->harga }}</p>

    <p><b>Rating:</b> {{ $menu->rating }}</p>

    <p><b>Description:</b> {{ $menu->deskripsi }}</p>

    <p><b>Status:</b> 
        {{ $menu->status_tersedia ? 'Available' : 'Not Available' }}
    </p>

</div>

@endsection