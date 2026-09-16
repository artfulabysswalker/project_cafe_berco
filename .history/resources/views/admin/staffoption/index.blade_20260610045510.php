@extends('dashboard')

@push('styles')
<style>
    :root {
        --berco-brown: #6B3F1F;
        --berco-amber: #D4A574;
        --berco-cream: #FFF8F0;
        --berco-dark-brown: #4A2C1F;
        --berco-light-brown: #A0683A;
        --transition-smooth: 0.3s ease;
    }

    .section-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(107, 63, 31, 0.08);
        border: 1px solid rgba(212, 165, 116, 0.15);
        margin-bottom: 2rem;
    }

    .section-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 2px solid rgba(212, 165, 116, 0.15);
        color: white;
    }

    .section-card.admins .section-header {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }

    .section-card.staffs .section-header {
        background: linear-gradient(135deg, #0066cc, #0052a3);
    }

    .section-card.customers .section-header {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .section-body {
        padding: 1.75rem;
    }

    .staff-username {
        font-family: monospace;
        background: rgba(212,165,116,0.1);
        padding: 2px 6px;
        border-radius: 5px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .btn-sm {
        padding: 5px 10px;
        border-radius: 6px;
        border: none;
    }

    .btn-warning {
        background: #0066cc;
        color: white;
    }

    .btn-primary {
        background: #A0683A;
        color: white;
    }

    .dropdown-menu {
        font-size: 13px;
    }
</style>
@endpush

@section('content')

<!-- ADMIN -->
<div class="section-card admins">
    <div class="section-header">
        <h5>👑 Admin</h5>
    </div>

    <div class="section-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach($admins as $person)
                <tr>
                    <td>{{ $person->name }}</td>
                    <td><span class="staff-username">{{ $person->username }}</span></td>
                    <td>

                        <!-- DROPDOWN EDIT -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
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

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- STAFF -->
<div class="section-card staffs">
    <div class="section-header">
        <h5>👨‍💼 Staff</h5>
    </div>

    <div class="section-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach($staffs as $person)
                <tr>
                    <td>{{ $person->name }}</td>
                    <td><span class="staff-username">{{ $person->username }}</span></td>
                    <td>

                        <!-- DROPDOWN EDIT -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
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

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CUSTOMER -->
<div class="section-card customers">
    <div class="section-header">
        <h5>👤 Customer</h5>
    </div>

    <div class="section-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach($users as $person)
                <tr>
                    <td>{{ $person->name }}</td>
                    <td><span class="staff-username">{{ $person->username }}</span></td>
                    <td>{{ $person->email }}</td>
                    <td><span class="badge bg-success">Customer</span></td>

                    <td>

                        <!-- UBAH ROLE (KEEP THIS EXACTLY) -->
                        <form method="POST"
                              action="{{ route('admin.staff.role', $person->id_user) }}"
                              style="display:flex;gap:6px;align-items:center;">

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

                            <button class="btn btn-primary btn-sm">
                                Ubah
                            </button>

                        </form>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection