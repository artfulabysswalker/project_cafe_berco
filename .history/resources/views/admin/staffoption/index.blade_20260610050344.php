@extends('dashboard')

@section('page-title', 'Manajemen Menu')
@section('breadcrumb', 'Menu')

@section('content')

<!-- 🔐 MY ACCOUNT SETTINGS (NOW INSIDE CONTENT) -->
<div class="section-card" style="margin-bottom: 20px;">

    <div class="section-header" style="background: linear-gradient(135deg, #6B3F1F, #A0683A);">
        🔐 My Account Settings
    </div>

    <div class="section-body">

        <div style="margin-bottom: 10px; font-weight: 600;">
            Logged in as: {{ auth()->user()->name }} ({{ auth()->user()->username }})
        </div>

        <form method="POST" action="{{ route('admin.password.update') }}" style="max-width: 400px;">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 10px;">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div style="margin-bottom: 10px;">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div style="margin-bottom: 10px;">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-primary btn-sm">
                Change Password
            </button>
        </form>

    </div>
</div>

<!-- HEADER -->
<form method="GET"
      action="{{ route('admin.menu') }}"
      style="margin-bottom:14px; display:flex; gap:8px; flex-wrap:wrap;">

    <input type="text"
           name="search"
           value="{{ request('search') }}"
           placeholder="Search menu by name..."
           style="padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:12px;min-width:220px;">

    <button type="submit" class="add-btn">
        <i class="ti ti-search"></i> Search
    </button>

    @if(request('search'))
        <a href="{{ route('admin.menu') }}"
           class="add-btn"
           style="background:#999;">
            Reset
        </a>
    @endif

</form>

<!-- FILTER + ADD -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">

    <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div class="filter-chip active">Semua</div>
        <div class="filter-chip">Kopi</div>
        <div class="filter-chip">Non Kopi</div>
        <div class="filter-chip">Ice Blended</div>
        <div class="filter-chip">Snack</div>
    </div>

    <a href="{{ route('admin.menu.create') }}" class="add-btn">
        <i class="ti ti-plus"></i> Tambah Menu
    </a>

</div>

<!-- GRID -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">

@forelse($menus as $menu)

    <div class="menu-card">

        <div class="menu-img" style="background:linear-gradient(135deg,#FAEEDA,#F5C4B3)">
            {{ $menu->icon ?? '☕' }}
        </div>

        <div class="menu-body">

            <div class="menu-name">{{ $menu->nama_menu }}</div>
            <div class="menu-cat">{{ $menu->kategori ?? 'Kopi' }}</div>

            @if($menu->discount_price && now()->between($menu->discount_start, $menu->discount_end))
                <div class="menu-price">
                    <span style="text-decoration:line-through;color:#999;font-size:12px;">
                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                    </span>

                    <span style="color:#D4752C;font-weight:700;">
                        Rp {{ number_format($menu->discount_price, 0, ',', '.') }}
                    </span>
                </div>
                <div style="font-size:10px;color:#D4752C;">🔥 Discount Active</div>
            @else
                <div class="menu-price">
                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                </div>
            @endif

            <div class="menu-footer">

                <a href="{{ route('admin.menu.show', $menu->id_menu) }}" class="act-btn">
                    <i class="ti ti-pencil"></i> Edit
                </a>

                <a href="{{ route('admin.menu.discount', $menu->id_menu) }}" class="act-btn">
                    <i class="ti ti-discount"></i> Discount
                </a>

                <form method="POST"
                      action="{{ route('admin.menu.delete', $menu->id_menu) }}"
                      onsubmit="return confirm('Hapus menu ini?')"
                      class="delete-form">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="act-btn danger">
                        <i class="ti ti-trash"></i> Delete
                    </button>

                </form>

            </div>

        </div>
    </div>

@empty

    <p>Tidak ada menu</p>

@endforelse

</div>

@endsection