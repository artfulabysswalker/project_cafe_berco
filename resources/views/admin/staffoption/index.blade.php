@extends('dashboard')

@section('content')

<h2>Staff Management</h2>

<h3>Admins</h3>

@foreach($admins as $person)

<div style="margin-bottom:15px;">

    <strong>{{ $person->name }}</strong>

    <form method="POST"
          action="{{ route('admin.staff.role', $person->id_user) }}"
          style="display:inline;">

        @csrf
        @method('PUT')

        <select name="id_role">

            @foreach($roles as $role)

                <option
                    value="{{ $role->id_role }}"
                    {{ $person->id_role == $role->id_role ? 'selected' : '' }}>
                    {{ $role->role_name }}
                </option>

            @endforeach

        </select>

        <button type="submit">
            Change Role
        </button>

    </form>

</div>

@endforeach


<hr>


<h3>Staff</h3>

@foreach($staffs as $person)

<div style="margin-bottom:15px;">

    <strong>{{ $person->name }}</strong>

    <form method="POST"
          action="{{ route('admin.staff.role', $person->id_user) }}"
          style="display:inline;">

        @csrf
        @method('PUT')

        <select name="id_role">

            @foreach($roles as $role)

                <option
                    value="{{ $role->id_role }}"
                    {{ $person->id_role == $role->id_role ? 'selected' : '' }}>
                    {{ $role->role_name }}
                </option>

            @endforeach

        </select>

        <button type="submit">
            Change Role
        </button>

    </form>

</div>

@endforeach


<hr>


<h3>Users</h3>

@foreach($users as $person)

<div style="margin-bottom:15px;">

    <strong>{{ $person->name }}</strong>

    <form method="POST"
          action="{{ route('admin.staff.role', $person->id_user) }}"
          style="display:inline;">

        @csrf
        @method('PUT')

        <select name="id_role">

            @foreach($roles as $role)

                <option
                    value="{{ $role->id_role }}"
                    {{ $person->id_role == $role->id_role ? 'selected' : '' }}>
                    {{ $role->role_name }}
                </option>

            @endforeach

        </select>

        <button type="submit">
            Change Role
        </button>

    </form>

</div>

@endforeach

@endsection