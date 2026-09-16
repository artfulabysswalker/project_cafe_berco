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

{{-- ITEMS (SAFE VERSION) --}}
@if($order->items ?? false)
    @foreach($order->items as $item)
        <p style="display:flex;justify-content:space-between;">
            <span>
                {{ $item->menu->name ?? 'Item' }} x{{ $item->quantity }}
            </span>
            <span>
                {{ number_format($item->subtotal ?? 0) }}
            </span>
        </p>
    @endforeach
@else
    <p style="color:#999;">No item data stored in history</p>
@endif

<hr>

{{-- TOTAL --}}
<p><strong>Total: {{ number_format($order->total_harga ?? 0) }}</strong></p>

<hr>

{{-- FOOTER --}}
<p>{{ $settings->footer_message ?? '' }}</p>

<p>WiFi: {{ $settings->wifi_name ?? '-' }}</p>
<p>Pass: {{ $settings->wifi_password ?? '-' }}</p>

{{--