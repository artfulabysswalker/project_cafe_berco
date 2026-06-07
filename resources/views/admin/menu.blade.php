@extends('dashboard')

@section('page-title', 'Manajemen Menu')
@section('breadcrumb', 'Menu')

@section('content')

<!-- Header dengan Filter dan Tombol Tambah -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
    <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div class="filter-chip active">Semua</div>
        <div class="filter-chip">Kopi</div>
        <div class="filter-chip">Non Kopi</div>
        <div class="filter-chip">Ice Blended</div>
        <div class="filter-chip">Snack</div>
    </div>
    <a href="{{ route('admin.menu.create') }}" class="add-btn"><i class="ti ti-plus"></i> Tambah Menu</a>
</div>

<!-- Menu Cards Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
    @forelse($menus as $menu)
        <div class="menu-card">
            <div class="menu-img" style="background:linear-gradient(135deg,#FAEEDA,#F5C4B3)">
                {{ $menu->icon ?? '☕' }}
            </div>
            <div class="menu-body">
                <div class="menu-name">{{ $menu->nama_menu }}</div>
                <div class="menu-cat">{{ $menu->kategori ?? 'Kopi' }}</div>
                <div class="menu-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                <div class="menu-footer">
                    <a href="{{ route('admin.menu.show', $menu->id_menu) }}" class="act-btn" title="Edit">
                        <i class="ti ti-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.menu.delete', $menu->id_menu) }}" style="display:inline;flex:1" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="act-btn danger" title="Hapus" style="width:100%;justify-content:center">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        @for($i = 0; $i < 3; $i++)
            <div class="menu-card">
                <div class="menu-img" style="background:linear-gradient(135deg,#FAEEDA,#F5C4B3)">{{ ['☕','🥛','⚡'][$i] }}</div>
                <div class="menu-body">
                    <div class="menu-name">{{ ['Cappuccino','Latte','Ice Blended Oreo'][$i] }}</div>
                    <div class="menu-cat">{{ ['Kopi','Kopi','Ice Blended'][$i] }}</div>
                    <div class="menu-price">Rp {{ [18000, 16500, 21000][$i] }}</div>
                    <div class="menu-footer">
                        <button class="act-btn"><i class="ti ti-pencil"></i></button>
                        <button class="act-btn danger"><i class="ti ti-trash"></i></button>
                    </div>
                </div>
            </div>
        @endfor
    @endforelse

    <!-- Add New Menu Card -->
    <div class="menu-card" style="border:1.5px dashed #ddd;display:flex;align-items:center;justify-content:center;cursor:pointer;background:transparent" onclick="window.location.href='{{ route('admin.menu.create') }}'">
        <div style="text-align:center;color:var(--color-text-secondary)">
            <div style="font-size:28px;margin-bottom:8px"><i class="ti ti-plus"></i></div>
            <div style="font-size:12px">Tambah Menu Baru</div>
        </div>
    </div>
</div>

<style>
    .filter-chip {
        font-size: 11px;
        padding: 5px 12px;
        border-radius: 20px;
        border: 0.5px solid var(--color-border-tertiary);
        cursor: pointer;
        color: var(--color-text-secondary);
        background: transparent;
        transition: all 0.15s ease;
    }
    .filter-chip.active {
        background: #D4752C;
        color: #fff;
        border-color: #D4752C;
    }
    .add-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 7px;
        font-size: 12px;
        background: #D4752C;
        color: #fff;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
    }
    .add-btn:hover {
        background: #c26620;
    }
    .menu-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        overflow: hidden;
    }
    .menu-img {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 44px;
    }
    .menu-body {
        padding: 10px 12px;
    }
    .menu-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text-primary);
    }
    .menu-cat {
        font-size: 10px;
        color: var(--color-text-secondary);
        margin: 2px 0 6px;
    }
    .menu-price {
        font-size: 13px;
        font-weight: 600;
        color: #D4752C;
        margin-bottom: 6px;
    }
    .menu-footer {
        display: flex;
        gap: 6px;
        padding-top: 6px;
        border-top: 0.5px solid var(--color-border-tertiary);
    }
    .act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 11px;
        border: 0.5px solid var(--color-border-tertiary);
        cursor: pointer;
        color: var(--color-text-secondary);
        background: transparent;
        text-decoration: none;
        transition: all 0.15s ease;
        flex: 1;
    }
    .act-btn:hover {
        background: var(--color-background-secondary);
    }
    .act-btn.danger {
        background: #FCEBEB;
        color: #791F1F;
        border-color: #F09595;
    }
</style>

@endsection