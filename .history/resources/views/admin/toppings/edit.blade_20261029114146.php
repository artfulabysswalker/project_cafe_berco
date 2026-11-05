
@extends('dashboard')

@section('page-title', 'Topping Management')
@section('breadcrumb', 'Toppings')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-stone-900 tracking-tight">Edit Topping</h2>
            <p class="text-xs font-mono text-stone-500 mt-1">Update topping details, portion sizes, and menu availability.</p>
        </div>

        <a href="{{ route('admin.toppings.index') }}"
           class="px-4 py-2 text-xs font-mono border border-stone-200 rounded-lg text-stone-700 hover:bg-stone-100 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>BACK
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.toppings.update', $topping) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.toppings._form', ['buttonText' => 'Update Topping'])
    </form>
</div>
@endsection
