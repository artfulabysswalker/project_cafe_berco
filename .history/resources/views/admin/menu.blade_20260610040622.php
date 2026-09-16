@extends('dashboard')

@section('page-title', 'Manajemen Menu')
@section('breadcrumb', 'Menu')

@section('content')

<!-- HEADER -->
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

        <!-- IMAGE -->
        <div class="menu-img" style="background:linear-gradient(135deg,#FAEEDA,#F5C4B3)">
            {{ $menu->icon ?? '☕' }}
        </div>

        <!-- BODY -->
        <div class="menu-body">

            <!-- NAME -->
            <div class="menu-name">
                {{ $menu->nama_menu }}
            </div>

            <!-- CATEGORY -->
            <div class="menu-cat">
                {{ $menu->kategori ?? 'Kopi' }}
            </div>

            <!-- PRICE (WITH DISCOUNT SUPPORT) -->
            @if($menu->discount_price && now()->between($menu->discount_start, $menu->discount_end))
                <div class="menu-price">

                    <span style="text-decoration:line-through;color:#999;font-size:12px;">
                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                    </span>

                    <span style="color:#D4752C;font-weight:700;">
                        Rp {{ number_format($menu->discount_price, 0, ',', '.') }}
                    </span>

                </div>

                <div style="font-size:10px;color:#D4752C;margin-bottom:6px;">
                    🔥 Discount Active
                </div>

            @else
                <div class="menu-price">
                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                </div>
            @endif


            <!-- FOOTER ACTIONS -->
           <div class="menu-footer">

    <a href="{{ route('admin.menu.show', $menu->id_menu) }}"
       class="act-btn">
        <i class="ti ti-pencil"></i>
        <span>Edit</span>
    </a>

    <a href="{{ route('admin.menu.discount', $menu->id_menu) }}"
       class="act-btn">
        <i class="ti ti-discount"></i>
        <span>Discount</span>
    </a>

    <form method="POST"
          action="{{ route('admin.menu.delete', $menu->id_menu) }}"
          onsubmit="return confirm('Hapus menu ini?')"
          style="flex:1; display:flex;">

        @csrf
        @method('DELETE')

        <button type="submit" class="act-btn danger">
            <i class="ti ti-trash"></i>
            <span>Delete</span>
        </button>

    </form>

</div>

@empty

    @for($i = 0; $i < 3; $i++)
        <div class="menu-card">

            <div class="menu-img" style="background:linear-gradient(135deg,#FAEEDA,#F5C4B3)">
                {{ ['☕', '🥛', '⚡'][$i] }}
            </div>

            <div class="menu-body">

                <div class="menu-name">
                    {{ ['Cappuccino', 'Latte', 'Ice Blended Oreo'][$i] }}
                </div>

                <div class="menu-cat">
                    {{ ['Kopi', 'Kopi', 'Ice Blended'][$i] }}
                </div>

                <div class="menu-price">
                    Rp {{ number_format([18000, 16500, 21000][$i], 0, ',', '.') }}
                </div>

                <div class="menu-footer">

                    <button class="act-btn">
                        <i class="ti ti-pencil"></i>
                    </button>

                    <button class="act-btn">
                        <i class="ti ti-discount"></i>
                    </button>

                    <button class="act-btn danger">
                        <i class="ti ti-trash"></i>
                    </button>

                </div>

            </div>

        </div>
    @endfor

@endforelse


<!-- ADD CARD -->
<div class="menu-card"
     style="border:1.5px dashed #ddd;display:flex;align-items:center;justify-content:center;cursor:pointer;background:transparent"
     onclick="window.location.href='{{ route('admin.menu.create') }}'">

    <div style="text-align:center;color:var(--color-text-secondary)">

        <div style="font-size:28px;margin-bottom:8px">
            <i class="ti ti-plus"></i>
        </div>

        <div style="font-size:12px">
            Tambah Menu Baru
        </div>

    </div>

</div>

</div>


<!-- STYLE -->
<style>

.filter-chip {
    font-size: 11px;
    padding: 5px 12px;
    border-radius: 20px;
    border: 0.5px solid var(--color-border-tertiary);
    cursor: pointer;
    color: var(--color-text-secondary);
    background: transparent;
    transition: 0.15s ease;
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
    gap: 6px;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 11px;
    border: 0.5px solid var(--color-border-tertiary);
    color: var(--color-text-secondary);
    background: transparent;
    text-decoration: none;

    /* IMPORTANT FIX */
    flex: unset;
    white-space: nowrap;
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