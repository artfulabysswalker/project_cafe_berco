@extends('dashboard')

@section('content')

<div style="max-width:400px; margin:40px auto; background:white; padding:20px; border-radius:12px;">

    <h2>Change Password</h2>

    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf

        <input type="password" name="current_password" placeholder="Current Password" style="width:100%; margin:10px 0;">

        <input type="password" name="new_password" placeholder="New Password" style="width:100%; margin:10px 0;">

        <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" style="width:100%; margin:10px 0;">

        <button type="submit" style="width:100%; padding:10px; background:#5d2e1a; color:white;">
            Update Password
        </button>

    </form>

</div>

@endsection