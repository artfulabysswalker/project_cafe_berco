@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">Add New Topping</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.toppings.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Topping Name</label>

                    <input
                        type="text"
                        name="nama_topping"
                        class="form-control"
                        value="{{ old('nama_topping') }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Topping Image</label>

                    <input
                        type="file"
                        name="foto"
                        class="form-control"
                        accept="image/*"
                    >
                </div>

                <hr>

                <h5 class="mb-3">Topping Sizes</h5>

                @foreach(['Small', 'Medium', 'Large'] as $index => $size)

                    <div class="border rounded p-3 mb-3">

                        <h6>{{ $size }}</h6>

                        <input type="hidden"
                               name="sizes[{{ $index }}][size]"
                               value="{{ $size }}">

                        <div class="row">

                            <div class="col-md-4 mb-2">
                                <label>Quantity</label>
                                <input
                                    type="number"
                                    name="sizes[{{ $index }}][quantity]"
                                    class="form-control"
                                    min="0"
                                    step="any"
                                    required
                                >
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Unit</label>
                                <select
                                    name="sizes[{{ $index }}][unit]"
                                    class="form-select"
                                    required
                                >
                                    <option value="g">Gram (g)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Price (Rp)</label>
                                <input
                                    type="number"
                                    name="sizes[{{ $index }}][harga]"
                                    class="form-control"
                                    min="0"
                                    required
                                >
                            </div>

                        </div>
                    </div>

                @endforeach

                <div class="mb-3">
                    <label>Status</label>

                    <select name="status_tersedia" class="form-select">
                        <option value="1">Available</option>
                        <option value="0">Out of Stock</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('admin.toppings.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button class="btn btn-warning">
                        Save Topping
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection