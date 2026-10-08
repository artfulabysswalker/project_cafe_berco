<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Meja TIDAK divalidasi dari input form: server menentukan meja dari
     * token QR di session (lihat OrderController::resolveTableForRequest).
     */
    public function rules(): array
    {
        return [
            'service_type' => 'required|in:dine_in,take_away',
            'payment_method' => ['required', Rule::in(['cash', 'qris'])],
            'notes' => 'nullable|string|max:500',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{9,15}$/'],
            'table_id' => 'nullable|string|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'service_type' => 'tipe layanan',
            'payment_method' => 'metode pembayaran',
            'customer_name' => 'nama pelanggan',
            'customer_phone' => 'nomor telepon',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_name.string' => 'Nama pelanggan tidak valid.',
            'customer_name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'customer_phone.regex' => 'Nomor telepon harus terdiri dari 9-15 digit angka.',
            'customer_phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'service_type.in' => 'Tipe layanan tidak valid.',
            'payment_method.in' => 'Metode pembayaran hanya tunai atau QRIS.',
        ];
    }
}
