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

    /* ACCOUNT CARD */
    .account-card {
        background: linear-gradient(135deg, #6B3F1F, #A0683A);
        color: white;
        padding: 16px;
        border-radius: 14px;
        margin-bottom: 20px;
    }

    .account-card input {
        width: 100%;
        padding: 8px;
        border-radius: 8px;
        border: none;
        margin-top: 5px;
        margin-bottom: 10px;
    }

    .account-card button {
        background: #fff;
        color: #6B3F1F;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    .account-card label {
        font-size: 12px;
        margin-top: 8px;
        display: block;
    }
</style>
@endpush

@section('content')

<!-- ================= MY ACCOUNT ================= -->
<div class="account-card">

    <div style="font-weight:700; margin-bottom:10px;">
        🔐 My Account Settings
    </div>

    <div style="font-size:13px; margin-bottom:10px;">
        Logged in as: <b>{{ auth()->user()->name }}</b>
    </div>

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        @method('PUT')

        <!-- CURRENT -->
        <label>Current Password</label>
        <div style="position:relative;">
            <input type="password" id="current_password" name="current_password" required>
            <span onclick="togglePass('current_password')"
                  style="position:absolute;right:10px;top:55%;cursor:pointer;">
                👁
            </span>
        </div>

        <!-- NEW -->
        <label>New Password</label>
        <div style="position:relative;">
            <input type="password" id="password" name="password" required>
            <span onclick="togglePass('password')"
                  style="position:absolute;right:10px;top:55%;cursor:pointer;">
                👁
            </span>
        </div>

        <!-- CONFIRM -->
        <label>Confirm Password</label>
        <div style="position:relative;">
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <span onclick="togglePass('password_confirmation')"
                  style="position:absolute;right:10px;top:55%;cursor:pointer;">
                👁
            </span>
        </div>

        <button type="submit" style="margin-top:10px;">
            Change Password
        </button>
    </form>
</div>

<!-- ================= ADMIN ================= -->
<div class="section-card admins">
    <div class="section-header">👑 Admin</div>

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

                    <td style="display:flex;gap:6px;">
                        <form method="POST"
                              action="{{ route('admin.staff.role', $person->id_user) }}">
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

                        <a href="{{ route('admin.staff.password.edit', $person->id_user) }}"
                           class="btn btn-sm btn-warning">
                            Reset Password
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ================= STAFF ================= -->
<div class="section-card staffs">
    <div class="section-header">👨‍💼 Staff</div>

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

                    <td style="display:flex;gap:6px;">
                        <form method="POST"
                              action="{{ route('admin.staff.role', $person->id_user) }}">
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

                        <a href="{{ route('admin.staff.password.edit', $person->id_user) }}"
                           class="btn btn-sm btn-warning">
                            Reset Password
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ================= CUSTOMER ================= -->
<div class="section-card customers">
    <div class="section-header">👤 Customer</div>

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

                    <td style="display:flex;gap:6px;">
                        <form method="POST"
                              action="{{ route('admin.staff.role', $person->id_user) }}">
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

                        <a href="{{ route('admin.staff.password.edit', $person->id_user) }}"
                           class="btn btn-sm btn-warning">
                            Reset Password
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ================= SCRIPT ================= -->
<script>
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

@endsection