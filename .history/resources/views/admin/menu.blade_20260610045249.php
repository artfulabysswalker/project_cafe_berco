@extends('dashboard')

@section('page-title', 'Manajemen Staff')
@section('breadcrumb', 'Staff')

@section('content')

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

            <!-- DROPDOWN ACTION -->
            <div class="dropdown">
                <button class="act-btn dropdown-toggle" data-bs-toggle="dropdown">
                    Actions
                </button>

                <ul class="dropdown-menu">

                    <li>
                        <a class="dropdown-item"
                           href="{{ route('admin.staffoption.edit', $person->id_user) }}">
                            Edit
                        </a>
                    </li>

                </ul>
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

            <!-- DROPDOWN ACTION -->
            <div class="dropdown">
                <button class="act-btn dropdown-toggle" data-bs-toggle="dropdown">
                    Actions
                </button>

                <ul class="dropdown-menu">

                    <li>
                        <a class="dropdown-item"
                           href="{{ route('admin.staffoption.edit', $person->id_user) }}">
                            Edit
                        </a>
                    </li>

                </ul>
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

            <!-- ROLE CHANGE ONLY -->
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

@endsection