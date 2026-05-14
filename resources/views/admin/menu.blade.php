@extends('dashboard')

@section('content')

    <style>
        /* ONLY CONTENT STYLING (NO LAYOUT) */

        .menu-list {
            max-width: 600px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* MENU CARD */
        .menu-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 12px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .menu-card:hover {
            background: #f2d9a6;
        }

        .menu-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-img {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #5d2e1a;
        }

        .menu-title {
            font-size: 14px;
            font-weight: 500;
        }

        .menu-actions button {
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 5px;
        }

        /* SIDEBAR BUTTON AREA (just visual) */
        .create-btn {
            margin-top: 20px;
            padding: 12px;
            background: #B7791F;
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
        }

        .create-btn:hover {
            background: #92400E;
        }

        .menu-page-offset {
            margin-left: 20px;
        }

        .main {
            padding-left: 40px;
        }
    </style>

    <!-- CONTENT ONLY (NO FLEX LAYOUT HERE) -->
    <div>
        <div class="menu-page-offset">
            <div class="menu-list">
                ...
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:20px;">
                <a href="{{ route('admin.menu.create') }}"
                    class="menu {{ request()->routeIs('admin.menu.create') ? 'active' : '' }}">
                    Create Menu
                </a>

                @if(session('success'))
                    <div style="padding:10px; background:green; color:white; margin-bottom:15px; border-radius:8px;">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        @foreach($menus as $menu)

            <div class="menu-card" onclick="selectMenu({{ $menu->id_menu }})">

                <div class="menu-left">

                    <div class="menu-img">
                        @if($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}" width="50" height="50">
                        @endif
                    </div>

                    <div class="menu-title">
                        {{ $menu->nama_menu }}
                    </div>

                </div>

                <div class="menu-actions">

                    <!-- VIEW -->
                    <button onclick="event.stopPropagation(); window.location='{{ route('admin.menu.show', $menu->id_menu) }}'">
                        👁
                    </button>

                    <!-- EDIT -->
                    <button onclick="event.stopPropagation(); window.location='{{ route('admin.menu.edit', $menu->id_menu) }}'">
                        ✎
                    </button>

                    <!-- DELETE -->
                    <form method="POST" action="{{ route('admin.menu.delete', $menu->id_menu) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button type="submit" onclick="event.stopPropagation(); return confirm('Delete this menu?')">
                            🗑
                        </button>
                    </form>

                </div>
            </div>

        @endforeach

    </div>

    </div>

    <script>
        function selectMenu(id) {
            console.log("Selected menu:", id);
        }

        function viewMenu(id) {
            alert("View menu " + id);
        }

        function editMenu(id) {
            alert("Edit menu " + id);
        }

        function deleteMenu(id) {
            alert("Delete menu " + id);
        }

        function createMenu() {
            alert("Create menu clicked");
        }
    </script>

@endsection