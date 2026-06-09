@extends('dashboard')

@section('page-title', 'Receipt Preview')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">

    <h3>Receipt Preview</h3>

    <button onclick="printReceipt()" class="act-btn primary">
        🖨️ Print
    </button>

</div>

<!-- PRINT AREA -->
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
    <p>Date: {{ $order->tanggal }}</p>

    <hr>

    {{-- ITEMS --}}
    @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;">
            <span>{{ $item->menu->name }} x{{ $item->quantity }}</span>
            <span>{{ number_format($item->subtotal) }}</span>
        </div>
    @endforeach

    <hr>

    {{-- TOTAL --}}
    <div style="display:flex;justify-content:space-between;font-weight:bold;">
        <span>Total</span>
        <span>Rp {{ number_format($order->total_harga) }}</span>
    </div>

    <hr>

    {{-- FOOTER --}}
    <p style="text-align:center">{{ $settings->footer_message ?? '' }}</p>

    <p style="text-align:center">
        WiFi: {{ $settings->wifi_name ?? '-' }} <br>
        Pass: {{ $settings->wifi_password ?? '-' }}
    </p>

</div>

{{-- PRINT SCRIPT --}}
<script>
function printReceipt() {
    const printContent = document.getElementById('receipt-area').innerHTML;
    const original = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = original;
    location.reload();
}
</script>

@endsection