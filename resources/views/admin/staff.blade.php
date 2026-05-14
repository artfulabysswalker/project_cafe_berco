@extends('dashboard')

@section('content')

<h1>Staff Management</h1>

<a href="/control/staff/create" style="padding:10px; background:green; color:white;">+ Add Staff</a>

<table border="1" width="100%" cellpadding="10" style="margin-top:20px;">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Action</th>
    </tr>

    @foreach($staff as $user)
    <tr>
        <td>{{ $user->id_user }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->username }}</td>
        <td>{{ $user->role->role_name ?? '-' }}</td>
        <td>
            <a href="/control/staff/delete/{{ $user->id_user }}" style="color:red;">Delete</a>
        </td>
    </tr>
    @endforeach

</table>

@endsection