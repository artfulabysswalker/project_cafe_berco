@extends('dashboard')

@push('styles')
<style>
    .section-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #eee;
        margin-bottom: 20px;
    }

    .section-header {
        padding: 14px 18px;
        font-weight: 600;
        color: #fff;
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
        padding: 16px;
    }

    .staff-username {
        font-family: monospace;
        background: #f6f6f6;
        padding: 2px 6px;
        border-radius: 5px;
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 12px;
    }

    .form-select-sm {
        font-size: 12px;
        padding: 4px 6px;
    }
</style>
@endpush

@section('content')

<!-- ================= ADMIN ================= -->
<div class="section-card admins">
    <div class="section-header">
        👑 Admin
    </div>

    <div class="section-body">
        <table class="table table-hover">
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
            @foreach($admins as $person)
                <tr>
                    <td>{{ $person->name }}</td>
                    <td><span class="staff-username">{{ $person->username }}</span></td>
                    <td>{{ $person->email }}</td>
                    <td><span class="badge bg-danger">Admin</span></td>

                    <td>
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

                            <button class="btn btn-primary btn-sm">Ubah</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ================= STAFF ================= -->
<div class="section-card staffs">
    <div class="section-header">
        👨‍💼 Staff
    </div>

    <div class="section-body">
        <table class="table table-hover">
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
            @foreach($staffs as $person)
                <tr>
                    <td>{{ $person->name }}</td>
                    <td><span class="staff-username">{{ $person->username }}</span></td>
                    <td>{{ $person->email }}</td>
                    <td><span class="badge bg-info">Staff</span></td>

                    <td>
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

                            <button class="btn btn-primary btn-sm">Ubah</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ================= CUSTOMER ================= -->
<div class="section-card customers">
    <div class="section-header">
        👤 Customer
    </div>

    <div class="section-body">
        <table class="table table-hover">
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

                            <button class="btn btn-primary btn-sm">Ubah</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection