<h2>Receipt Preview</h2>

<div style="width:280px;border:1px solid black;padding:10px;font-family:monospace">

   @if($settings->logo)
       <img src="{{ asset('storage/'.$settings->logo) }}" width="80">
   @endif

   <h3>{{ $settings->cafe_name }}</h3>
   <p>{{ $settings->address }}</p>

   <hr>

   @foreach($order->items as $item)
       <p>
           {{ $item->menu->name }} x{{ $item->quantity }}
           <span style="float:right">{{ $item->subtotal }}</span>
       </p>
   @endforeach

   <hr>

   <p><strong>Total: {{ $order->total_harga }}</strong></p>

   <hr>

   <p>{{ $settings->footer_message }}</p>

   <p>WiFi: {{ $settings->wifi_name }}</p>
   <p>Pass: {{ $settings->wifi_password }}</p>

</div>

<br>

<a href="{{ route('receipt.print', $order->id_order) }}" target="_blank">
   🖨️ Print
</a>

<a href="{{ route('receipt.pdf', $order->id_order) }}">
   📄 Download PDF
</a>
