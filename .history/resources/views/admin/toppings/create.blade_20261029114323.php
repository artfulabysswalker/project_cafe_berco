@extends('dashboard')

@section('page-title', 'Topping Management')
@section('breadcrumb', 'Toppings')

@section('content')
    {{-- Form content goes here --}}
@endsection
@section('content')
<div class="container py-4">
    <h2 class="mb-4">Create Topping</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.toppings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.toppings._form', ['buttonText' => 'Save Topping'])
    </form>
</div>
@endsection