@extends('Customerviews.layouts.web')

@section('title', 'Bayar QRIS - Berco Cafe')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-stone-100">
    <main class="container" style="max-width: 620px; margin: 0 auto; padding: 40px 16px;">
        <livewire:checkout-qris-page :order="$order" />
    </main>
</div>
@endsection
