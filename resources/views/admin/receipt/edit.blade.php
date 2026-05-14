<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    padding: 20px;
}

h1, h2 {
    margin-bottom: 10px;
}

form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    max-width: 400px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

input, textarea {
    width: 100%;
    padding: 8px;
    margin: 6px 0 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    background: #007bff;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #0056b3;
}

.preview {
    width: 280px;
    background: white;
    border: 1px solid #ccc;
    padding: 10px;
    font-family: monospace;
    margin-top: 20px;
}

.center {
    text-align: center;
}

hr {
    border-top: 1px dashed black;
}
</style>
<h1>Receipt Settings</h1>

@if(session('success'))
   <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('admin.receipt.update') }}" enctype="multipart/form-data">
   @csrf

   <h3>Logo</h3>
   <input type="file" name="logo">
   @if($settings->logo)
       <img src="{{ asset('storage/' . $settings->logo) }}" width="80">
   @endif

   <h3>Info</h3>
   <input name="cafe_name" value="{{ $settings->cafe_name }}">
   <input name="address" value="{{ $settings->address }}">
   <input name="phone" value="{{ $settings->phone }}">

   <textarea name="footer_message">{{ $settings->footer_message }}</textarea>

   <h3>WiFi</h3>
   <input name="wifi_name" value="{{ $settings->wifi_name }}">
   <input name="wifi_password" value="{{ $settings->wifi_password }}">

   <button type="submit">Save</button>
</form>
<div class="preview">

   @if($settings->logo)
       <div class="center">
           <img src="{{ asset('storage/' . $settings->logo) }}" width="80">
       </div>
   @endif

   <div class="center">
       <h3>{{ $settings->cafe_name }}</h3>
       <p>{{ $settings->address }}</p>
       <p>{{ $settings->phone }}</p>
   </div>

   <hr>

   <p>Latte x1 <span style="float:right">20000</span></p>
   <p>Croissant x1 <span style="float:right">15000</span></p>

   <hr>

   <strong>Total <span style="float:right">35000</span></strong>

   <hr>

   <div class="center">
       <p>{{ $settings->footer_message }}</p>
       <p>WiFi: {{ $settings->wifi_name }}</p>
       <p>Pass: {{ $settings->wifi_password }}</p>
   </div>

</div>
