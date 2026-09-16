@extends('dashboard')

@section('page-title', 'Receipt Preview')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">

    <h3>Receipt Preview</h3>

    <div style="display:flex;gap:10px;">

        {{-- PRINT --}}
        <a href="{{ route('admin.receipt.print', $order->id_order) }}"
           target="_blank"
           class="btn btn-primary">
            🖨️ Print
        </a>

        {{-- BROWSER PRINT --}}
        <button onclick="window.print()" class="btn">
            Quick Print
        </button>

    </div>

</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- RECEIPT CONTENT -->
    <div style="background:#fff;padding:15px;border:1px dashed #ccc;font-family:monospace;">

        {{-- HEADER --}}
        <div style="text-align:center;">

            @if($settings && $settings->logo)
                <img src="{{ asset('storage/'.$settings->logo) }}" style="max-height:60px;">
            @endif

            <h3>{{ $settings->cafe_name ?? 'Cafe' }}</h3>
            <p>{{ $settings->address ?? '-' }}</p>
            <p>{{ $settings->phone ?? '-' }}</p>

        </div>

        <hr>

        {{-- ORDER INFO --}}
        <div style="font-size:12px;">
            <p><strong>Order ID:</strong> #{{ $order->id_order }}</p>
            <p><strong>Customer:</strong> {{ $order->nama_pelanggan }}</p>
            <p><strong>Date:</strong> {{ $order->tanggal }}</p>
        </div>

        <hr>

        {{-- ITEMS --}}
        @if($order->items ?? false)
            @foreach($order->items as $item)
                <p style="display:flex;justify-content:space-between;font-size:12px;">
                    <span>
                        {{ $item->menu->name ?? 'Item' }} x{{ $item->quantity }}
                    </span>
                    <span>
                        {{ number_format($item->subtotal ?? 0) }}
                    </span>
                </p>
            @endforeach
        @else
            <p style="color:#999;font-size:12px;">No items available</p>
        @endif

        <hr>

        {{-- TOTAL --}}
        <p style="display:flex;justify-content:space-between;font-weight:bold;">
            <span>Total</span>
            <span>{{ number_format($order->total_harga ?? 0) }}</span>
        </p>

        <hr>

        {{-- FOOTER --}}
        <p style="text-align:center;font-size:12px;">
            {{ $settings->footer_message ?? '' }}
        </p>

        <p style="text-align:center;font-size:11px;">
            WiFi: {{ $settings->wifi_name ?? '-' }} |
            Pass: {{ $settings->wifi_password ?? '-' }}
        </p>

    </div>

    <!-- SIDE INFO (OPTIONAL PANEL) -->
    <div>

        <div class="form-group">
            <label>Status Order</label>
            <input type="text" class="form-input" value="{{ $order->status_order }}" disabled>
        </div>

        <div class="form-group">
            <label>Status Payment</label>
            <input type="text" class="form-input" value="{{ $order->status_pembayaran }}" disabled>
        </div>

        <div class="form-group">
            <label>Total</label>
            <input type="text" class="form-input" value="{{ number_format($order->total_harga) }}" disabled>
        </div>

    </div>

</div>

@endsection