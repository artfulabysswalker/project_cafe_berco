<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: monospace; width:280px; }
.center { text-align:center; }
hr { border-top:1px dashed black; }
</style>
</head>

<body onload="window.print()">

@if($settings->logo)
<div class="center">
   <img src="{{ public_path('storage/'.$settings->logo) }}" width="80">
</div>
@endif

<div class="center">
   <strong>{{ $settings->cafe_name }}</strong><br>
   {{ $settings->address }}<br>
   {{ $settings->phone }}
</div>

<hr>

@foreach($order->items as $item)
<div>
   {{ $item->menu->name }} x{{ $item->quantity }}
   <span style="float:right">{{ $item->subtotal }}</span>
</div>
@endforeach

<hr>

<strong>Total</strong>
<span style="float:right">{{ $order->total_harga }}</span>

<hr>

<div class="center">
   {{ $settings->footer_message }}
</div>

<div class="center">
   WiFi: {{ $settings->wifi_name }}<br>
   Pass: {{ $settings->wifi_password }}
</div>

</body>
</html>
