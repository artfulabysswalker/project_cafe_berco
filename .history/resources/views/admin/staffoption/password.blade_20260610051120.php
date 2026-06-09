@extends('dashboard')

@push('styles')
<style>
    .section-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eee;
        overflow: hidden;
    }

    .section-header {
        padding: 14px;
        font-weight: 600;
        background: linear-gradient(135deg, #6B3F1F, #A0683A);
        color: white;
    }

    .section-body {
        padding: 16px;
    }

    .info-box {
        background: #f7f7f7;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 13px;
    }

    .input-group {
        position: relative;
        margin-bottom: 12px;
    }

    .input-group input {
        width: 100%;
        padding: 10px 40px 10px 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 13px;
    }

    .toggle-eye {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 14px;
        color: #666;
    }

    .btn {
        background: #6B3F1F;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn:hover {
        background: #4A2C1F;
    }
</style>
@endpush

@section('content')

<div class="section-card">

    <div class="section-header">
        🔐 Reset Password User
    </div>

    <div class="section-body">

        <!-- USER INFO (NOT BLANK, SHOW DATA) -->
        <div class="info-box">
            <div><b>Name:</b> {{ $user->name }}</div>
            <div><b>Username:</b> {{ $user->username }}</div>
            <div><b>Email:</b> {{ $user->email }}</div>
            <div><b>Role ID:</b> {{ $user->id_role }}</div>
        </div>

        <form method="POST" action="{{ route('admin.staff.password.update', $user->id_user) }}">
            @csrf
            @method('PUT')

            <!-- PASSWORD -->
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="New Password" required>
                <span class="toggle-eye" onclick="togglePassword('password')">👁</span>
            </div>

            <!-- CONFIRM -->
            <div class="input-group">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                <span class="toggle-eye" onclick="togglePassword('password_confirmation')">👁</span>
            </div>

            <button class="btn">
                Update Password
            </button>

        </form>

    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
</script>

@endsection