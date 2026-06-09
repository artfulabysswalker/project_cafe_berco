@extends('dashboard')

@section('page-title', 'Edit Receipt')

@section('content')

<form method="POST" action="{{ route('receipt.update') }}" enctype="multipart/form-data">
@csrf

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- FORM -->
    <div>

        <div class="form-group">
            <label>Cafe Name</label>
            <input type="text" name="cafe_name" value="{{ $settings->cafe_name }}" class="form-input">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-input">{{ $settings->address }}</textarea>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ $settings->phone }}" class="form-input">
        </div>

        <div class="form-group">
            <label>Footer Message</label>
            <textarea name="footer_message" class="form-input">{{ $settings->footer_message }}</textarea>
        </div>

        <div class="form-group">
            <label>WiFi Name</label>
            <input type="text" name="wifi_name" value="{{ $settings->wifi_name }}" class="form-input">
        </div>

        <div class="form-group">
            <label>WiFi Password</label>
            <input type="text" name="wifi_password" value="{{ $settings->wifi_password }}" class="form-input">
        </div>

        <div class="form-group">
            <label>Logo</label>
            <input type="file" name="logo" class="form-input">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>

    </div>

    <!-- PREVIEW -->
    <div style="background:#fff;padding:15px;border:1px dashed #ccc;font-family:monospace;">

        <div style="text-align:center;">
            @if($settings->logo)
                <img src="{{ asset('storage/'.$settings->logo) }}" style="max-height:60px;">
            @endif

            <h3>{{ $settings->cafe_name }}</h3>
            <p>{{ $settings->address }}</p>
            <p>{{ $settings->phone }}</p>
        </div>

        <hr>

        <p style="text-align:center;">RECEIPT PREVIEW</p>

        <hr>

        <p style="text-align:center;">{{ $settings->footer_message }}</p>

    </div>

</div>

</form>

@endsection