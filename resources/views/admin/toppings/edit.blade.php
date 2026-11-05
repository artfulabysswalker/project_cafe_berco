@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">Edit Topping</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.toppings.update', $topping->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Topping Name</label>

                    <input
                        type="text"
                        name="nama_topping"
                        class="form-control"
                        value="{{ old('nama_topping', $topping->nama_topping) }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label>Current Image</label><br>

                    @if($topping->foto)
                        <img
                            src="{{ asset('storage/'.$topping->foto) }}"
                            width="100"
                            class="rounded mb-2"
                        >
                    @endif

                    <input
                        type="file"
                        name="foto"
                        class="form-control"
                        accept="image/*"
                    >
                </div>

                <hr>

                <h5>Topping Sizes</h5>

                @foreach(['Small', 'Medium', 'Large'] as $index => $sizeName)

                    @php
                        $size = $topping->sizes->firstWhere('size', $sizeName);
                    @endphp

                    <div class="border rounded p-3 mb-3">

                        <h6>{{ $sizeName }}</h6>

                        <input type="hidden"
                               name="sizes[{{ $index }}][id]"
                               value="{{ $size->id ?? '' }}">

                        <input type="hidden"
                               name="sizes[{{ $index }}][size]"
                               value="{{ $sizeName }}">

                        <div class="row">

                            <div class="col-md-4">
                                <label>Quantity</label>
                                <input
                                    type="number"
                                    name="sizes[{{ $index }}][quantity]"
                                    class="form-control"
                                    min="0"
                                    step="any"
                                    value="{{ $size->quantity ?? '' }}"
                                    required
                                >
                            </div>

                            <div class="col-md-4">
                                <label>Unit</label>
                                <select
                                    name="sizes[{{ $index }}][unit]"
                                    class="form-select"
                                >
                                    <option value="g"
                                        @selected(($size->unit ?? '') == 'g')>
                                        Gram (g)
                                    </option>

                                    <option value="ml"
                                        @selected(($size->unit ?? '') == 'ml')>
                                        Milliliter (ml)
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Price (Rp)</label>
                                <input
                                    type="number"
                                    name="sizes[{{ $index }}][harga]"
                                    class="form-control"
                                    min="0"
                                    value="{{ $size->harga ?? '' }}"
                                    required
                                >
                            </div>

                        </div>

                    </div>

                @endforeach

                <div class="mb-3">
                    <label>Status</label>

                    <select name="status_tersedia" class="form-select">
                        <option value="1"
                            @selected($topping->status_tersedia)>
                            Available
                        </option>

                        <option value="0"
                            @selected(!$topping->status_tersedia)>
                            Out of Stock
                        </option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('admin.toppings.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button class="btn btn-warning">
                        Update Topping
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection