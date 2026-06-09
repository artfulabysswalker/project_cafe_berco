<!-- Popular Menu (Based on Reviews) -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="ti ti-star"></i> Menu Terpopuler (Berdasarkan Review)
        </div>
    </div>

    <div style="padding:14px">
        @forelse($popularMenus as $menu)
            <div class="top-menu-row">
                <div class="top-menu-name">
                    {{ $menu->nama_menu }}
                </div>

                <div class="top-menu-bar">
                    <div class="top-menu-fill" style="width: {{ $menu->avg_rating * 20 }}%"></div>
                </div>

                <div class="top-menu-val">
                    ⭐ {{ number_format($menu->avg_rating, 1) }}
                </div>
            </div>
        @empty
            <p style="color:#777;font-size:12px;">Belum ada data review.</p>
        @endforelse
    </div>
</div>