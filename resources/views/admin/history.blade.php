@extends('dashboard')

@section('content')

<h2>Order History</h2>

<!-- 🔍 SEARCH (NEW FEATURE) -->
<form method="GET" action="{{ route('orders.history') }}" style="margin-bottom:20px;">

   <input type="text"
          name="search"
          placeholder="Search by customer or order ID"
          value="{{ request('search') }}">

   <input type="date"
          name="date"
          value="{{ request('date') }}">

   <!-- ✅ NEW: DateTime range -->
   <input type="datetime-local"
          name="from"
          value="{{ request('from') }}">

   <input type="datetime-local"
          name="to"
          value="{{ request('to') }}">

   <button type="submit">Search</button>

</form>

@foreach($historyOrders as $order)

<div style="border:1px solid #ccc;padding:15px;margin-bottom:15px;">

   <strong>Order #{{ $order->id_order }}</strong>

   <!-- ✅ IMPORTANT OLD DATA (KEPT) -->
   <p>Customer: {{ $order->nama_pelanggan }}</p>
   <p>Total: Rp {{ $order->total_harga }}</p>
   <p>Payment: {{ $order->status_pembayaran }}</p>
   <p>Status: {{ $order->status_order }}</p>

   <!-- ✅ NEW EXTRA INFO -->
   <p>Time: {{ $order->tanggal }}</p>

   <hr>

   <!-- ✅ NEW RECEIPT FEATURES -->
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

<!-- ✅ PAGINATION (NEW FEATURE) -->
{{ $historyOrders->links() }}

@endsection