@extends('dashboard')

@section('content')

<h2>Recent Orders (Pending)</h2>

@foreach($pendingOrders as $order)

<div style="border:1px solid #ccc;padding:15px;margin-bottom:15px;">

    <strong>Order #{{ $order->id_order }}</strong>

    <p>Status: {{ $order->status }}</p>

    <!-- Finish button -->
    <form method="POST"
          action="{{ route('admin.orders.complete',$order->id_order) }}"
          style="display:inline;">
        @csrf
        @method('PUT')

        <button type="submit">
            ✔ Finish
        </button>
    </form>

    <!-- Cancel button -->
    <form method="POST"
          action="{{ route('admin.orders.cancel',$order->id_order) }}"
          style="display:inline;">
        @csrf
        @method('PUT')

        <button onclick="return confirm('Cancel this order?')" type="submit">
            ✖ Cancel
        </button>
    </form>

    <br><br>

    <!-- ✅ NEW: Receipt Actions -->
    <a href="{{ route('receipt.view', $order->id_order) }}">
        👀 View Receipt
    </a>

    <a href="{{ route('receipt.print', $order->id_order) }}" target="_blank">
        🖨️ Print
    </a>

    <a href="{{ route('receipt.pdf', $order->id_order) }}">
        📄 PDF
    </a>

</div>

@endforeach


<hr>

<h2>Confirmed Orders</h2>

@foreach($confirmedOrders as $order)

<div style="border:1px solid #ccc;padding:15px;margin-bottom:15px;">

    <strong>Order #{{ $order->id_order }}</strong>

    <p>Status: {{ $order->status }}</p>

    <br>

    <!-- ✅ NEW: Receipt Actions (also here) -->
    <a href="{{ route('receipt.view', $order->id_order) }}">
        👀 View Receipt
    </a>

    <a href="{{ route('receipt.print', $order->id_order) }}" target="_blank">
        🖨️ Print
    </a>

    <a href="{{ route('receipt.pdf', $order->id_order) }}">
        📄 PDF
    </a>

</div>

@endforeach

@endsection