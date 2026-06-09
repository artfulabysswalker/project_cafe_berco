@extends('dashboard')

@section('page-title', 'Manajemen Staff')
@section('breadcrumb', 'Staff')

@section('content')

<!-- HEADER -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">

    <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div class="filter-chip active">All</div>
        <div class="filter-chip">Admin</div>
        <div class="filter-chip">Staff</div>
        <div class="filter-chip">Customer</div>
    </div>

    <a href="{{ route('admin.staffoption.create') }}" class="add-btn">
        <i class="ti ti-plus"></i> Tambah Staff
    </a>

</div>

<!-- GRID -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px">

@foreach($admins as $person)

    <div class="menu-card">
        <div class="menu-img" style="background:linear-gradient(135deg,#FDE2E2,#F8B4B4)">
            👑
        </div>

        <div class="menu-body">

            <div class="menu-name">{{ $person->name }}</div>
            <div class="menu-cat">{{ $person->username }}</div>

            <div class="menu-price">
                <span class="badge bg-danger">Admin</span>
            </div>

            <div class="menu-footer">

                <a href="{{ route('admin.staffoption.edit', $person->id_user) }}" class="act-btn">
                    <i class="ti ti-pencil"></i>
                    <span>Edit</span>
                </a>

                <form method="POST"
                      action="{{ route('admin.staff.destroy', $person->id_user) }}"
                      onsubmit="return confirm('Hapus admin ini?')"
                      class="delete-form">

                    @csrf
                    @method('DELETE')

                    <button class="act-btn danger">
                        <i class="ti ti-trash"></i>
                        <span>Delete</span>
                    </button>

                </form>

            </div>

        </div>
    </div>

@endforeach

@foreach($staffs as $person)

    <div class="menu-card">
        <div class="menu-img" style="background:linear-gradient(135deg,#DDEBFF,#A7C7FF)">
            👨‍💼
        </div>

        <div class="menu-body">

            <div class="menu-name">{{ $person->name }}</div>
            <div class="menu-cat">{{ $person->username }}</div>

            <div class="menu-price">
                <span class="badge bg-info">Staff</span>
            </div>

            <div class="menu-footer">

                <a href="{{ route('admin.staffoption.edit', $person->id_user) }}" class="act-btn">
                    <i class="ti ti-pencil"></i>
                    <span>Edit</span>
                </a>

                <form method="POST"
                      action="{{ route('admin.staff.destroy', $person->id_user) }}"
                      onsubmit="return confirm('Hapus staff ini?')"
                      class="delete-form">

                    @csrf
                    @method('DELETE')

                    <button class="act-btn danger">
                        <i class="ti ti-trash"></i>
                        <span>Delete</span>
                    </button>

                </form>

            </div>

        </div>
    </div>

@endforeach

@foreach($users as $person)

    <div class="menu-card">
        <div class="menu-img" style="background:linear-gradient(135deg,#DFF7E3,#B6F2C6)">
            👤
        </div>

        <div class="menu-body">

            <div class="menu-name">{{ $person->name }}</div>
            <div class="menu-cat">{{ $person->username }}</div>

            <div class="menu-price">
                <span class="badge bg-success">Customer</span>
            </div>

            <form method="POST"
                  action="{{ route('admin.staff.role', $person->id_user) }}"
                  style="display:flex;gap:6px;flex-direction:column">

                @csrf
                @method('PUT')

                <select name="id_role" class="form-select form-select-sm">
                    @foreach($roles as $role)
                        <option value="{{ $role->id_role }}"
                            {{ $person->id_role == $role->id_role ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>

                <button class="act-btn">
                    Ubah Role
                </button>

            </form>

        </div>
    </div>

@endforeach

</div>

<!-- ADD CARD -->
<div class="menu-card"
     style="border:1.5px dashed #ddd;display:flex;align-items:center;justify-content:center;cursor:pointer;background:transparent;margin-top:12px"
     onclick="window.location.href='{{ route('admin.staffoption.create') }}'">

    <div style="text-align:center;color:var(--color-text-secondary)">
        <div style="font-size:28px;margin-bottom:8px">
            <i class="ti ti-plus"></i>
        </div>
        <div style="font-size:12px">
            Tambah Staff Baru
        </div>
    </div>

</div>

@endsection