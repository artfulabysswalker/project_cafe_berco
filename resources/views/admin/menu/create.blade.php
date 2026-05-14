

@extends('dashboard')

@section('content')

<div style="max-width:500px; margin:40px auto; padding:20px; background:white; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <h2 style="margin-bottom:20px;">Create Menu</h2>

   <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom:15px;">

        @if ($errors->any())
    <div style="color:red; margin-bottom:15px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <input type="text" name="nama_menu" placeholder="Menu Name"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
        </div>

        <div style="margin-bottom:15px;">
            <input type="number" name="harga" placeholder="Price"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
        </div>

        <div style="margin-bottom:15px;">
            <textarea name="deskripsi" placeholder="Description"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;"></textarea>
        </div>

        <div style="margin-bottom:15px;">
            <input type="file" name="foto" placeholder="Image URL"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
        </div>

        <div style="margin-bottom:20px;">
            <input type="number" step="0.1" name="rating" placeholder="Rating (auto 0 if empty)"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
        </div>

        <button type="submit"
            style="width:100%; padding:12px; background:#5d2e1a; color:white; border:none; border-radius:8px; cursor:pointer;">
            Save Menu
        </button>

    </form>

</div>

@endsection