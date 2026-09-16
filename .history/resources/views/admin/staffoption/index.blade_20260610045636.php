@extends('dashboard')

@section('content')

<div class="section-card">

    <div class="section-header">
        <h5>👥 Manajemen User</h5>
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

                    <td>
                        <span class="staff-username">{{ $person->username }}</span>
                    </td>

                    <td>{{ $person->email }}</td>

                    <td>
                        @if($person->role->role_name == 'Admin')
                            <span class="badge bg-danger">Admin</span>
                        @elseif($person->role->role_name == 'Staff')
                            <span class="badge bg-info">Staff</span>
                        @else
                            <span class="badge bg-success">Customer</span>
                        @endif
                    </td>

                    <td>

                        <!-- SAME STYLE FOR ALL USERS -->
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