@extends('dashboard')

@section('page-title', 'Receipt Preview')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
    <h3>Receipt Preview</h3>

    <button onclick="window.print()" class="act-btn primary">
        🖨️ Print
    </button>
</div>

{{-- RECEIPT AREA --}}
<div id="receipt-area" style="background:#fff;padding:20px;border:1px dashed #ccc;font-family:monospace;">

    {{-- LOGO --}}
    @if($settings && $settings->logo)
        <div style="text-align:center;">
            <img src="{{ asset('storage/'.$settings->logo) }}" style="max-height:70px;">
        </div>
    @endif

    {{-- CAFE INFO --}}
    <div style="text-align:center;">
        <h3>{{ $settings->cafe_name ?? 'Cafe' }}</h3>
        <p>{{ $settings->address ?? '-' }}</p>
        <p>{{ $settings->phone ?? '-' }}</p>
    </div>

    <hr>

    {{-- ORDER INFO --}}
    <p>Order ID: #{{ $order->id_order }}</p>
    <p>Customer: {{ $order->nama_pelanggan }}</p>
    <p>Date: {{ $order->created_at ?? $order->tanggal }}</p>

    <hr>

    {{-- ITEMS (SAFE FOR HISTORY) --}}
    <p style="text-align:center;color:#999;">
        Item details are not stored in history
    </p>

    <hr>

    {{-- TOTAL --}}
    <div style="display:flex;justify-content:space-between;font-weight:bold;">
        <span>Total</span>
        <span>Rp {{ number_format($order->total_harga) }}</span>
    </div>

    <hr>

    {{-- PAYMENT --}}
    <div style="display:flex;justify-content:space-between;">
        <span>Payment</span>
        <span>{{ ucfirst($order->payment_method ?? '-') }}</span>
    </div>

    <div style="display:flex;justify-content:space-between;">
        <span>Status</span>
        <span>{{ ucfirst($order->status_order ?? '-') }}</span>
    </div>

    <hr>

    {{-- FOOTER --}}
    <p style="text-align:center">{{ $settings->footer_message ?? '' }}</p>

    <p style="text-align:center">
        WiFi: {{ $settings->wifi_name ?? '-' }} <br>
        Pass: {{ $settings->wifi_password ?? '-' }}
    </p>

</div>

{{-- PRINT ONLY RECEIPT --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #receipt-area, #receipt-area * {
        visibility: visible;
    }

    #receipt-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .act-btn {
        display: none !important;
    }
}
</style>

@endsection