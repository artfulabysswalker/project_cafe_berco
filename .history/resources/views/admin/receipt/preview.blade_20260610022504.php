<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">

    <h3>Receipt Preview</h3>

    <div>
        <a href="{{ route('admin.receipt.print', $order->id_order) }}" target="_blank" class="act-btn primary">
            <i class="ti ti-printer"></i> Print
        </a>
    </div>

</div>


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
<div style="text-align:right;margin-bottom:10px;">
    <button onclick="window.print()" class="act-btn primary">
        <i class="ti ti-printer"></i> Print
    </button>
</div>