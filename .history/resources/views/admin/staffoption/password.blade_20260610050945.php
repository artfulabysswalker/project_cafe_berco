@extends('dashboard')

@section('content')

<div class="section-card">
    <div class="section-header" style="background:#6B3F1F; color:white;">
        🔐 Reset Password - {{ $user->name }}
    </div>

    <div class="section-body">

        <p>
            Username: <b>{{ $user->username }}</b><br>
            Email: <b>{{ $user->email }}</b>
        </p>

        <form method="POST"
              action="{{ route('admin.staff.password.update', $user->id_user) }}">

            @csrf
            @method('PUT')

            <div style="margin-bottom:10px;">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div style="margin-bottom:10px;">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-primary">
                Update Password
            </button>

        </form>

    </div>
</div>

@endsection