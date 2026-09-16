<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">

    <h3>Receipt Preview</h3>

    <a href="{{ route('admin.receipt.print', $order->id_order) }}"
       target="_blank"
       class="act-btn primary">
        <i class="ti ti-printer"></i> Print
    </a>

</div>

{{-- LOGO --}}
@if($settings && $settings->logo)
    <img src="{{ asset('storage/'.$settings->logo) }}" width="80">
@endif

{{-- CAFE INFO --}}
<h3>{{ $settings->cafe_name ?? 'Cafe' }}</h3>
<p>{{ $settings->address ?? '-' }}</p>

<hr>

{{-- ITEMS --}}
@foreach($order->items as $item)
    <p style="display:flex;justify-content:space-between;">
        <span>{{ $item->menu->name }} x{{ $item->quantity }}</span>
        <span>{{ number_format($item->subtotal) }}</span>
    </p>
@endforeach

<hr>

{{-- TOTAL --}}
<p><strong>Total: {{ number_format($order->total_harga) }}</strong></p>

<hr>

{{-- FOOTER --}}
<p>{{ $settings->footer_message ?? '' }}</p>

<p>WiFi: {{ $settings->wifi_name ?? '-' }}</p>
<p>Pass: {{ $settings->wifi_password ?? '-' }}</p>

{{-- PRINT BUTTON (OPTIONAL CLEAN) --}}
<div style="text-align:right;margin-top:15px;">
    <button onclick="window.print()" class="act-btn primary">
        🖨️ Print
    </button>
</div>