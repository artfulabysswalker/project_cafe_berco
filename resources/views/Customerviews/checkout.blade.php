@extends('Customerviews.layouts.web')

@section('title', 'Pembayaran - Berco Cafe')

@section('content')
<div class="checkout-page">
    <main class="container checkout-container">
        <div class="checkout-header">
            <h1>Pembayaran</h1>
            <p>Selesaikan pesanan Anda</p>
        </div>

        <div class="checkout-content">
            <div class="checkout-options">
                <section class="checkout-card">
                    <h3>Tipe Layanan</h3>
                    <div class="option-group">
                        <label class="option-item">
                            <input type="radio" name="service_type" value="dine_in" checked>
                            <div class="option-info">
                                <i class="fas fa-utensils" style="color: #e67e22;"></i>
                                <div>
                                    <strong>Makan di Tempat</strong>
                                    <span>Nikmati di kafe kami</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="service_type" value="take_away">
                            <div class="option-info">
                                <i class="fas fa-shopping-bag" style="color: #3498db;"></i>
                                <div>
                                    <strong>Bawa Pulang</strong>
                                    <span>Ambil dan bawa pulang</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </section>
                
                <section class="checkout-card">
                    <h3>Metode Pembayaran</h3>
                    <div class="option-group">
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="cash" checked>
                            <div class="option-info">
                                <i class="fas fa-money-bill-wave" style="color: #27ae60;"></i>
                                <div>
                                    <strong>Tunai</strong>
                                    <span>Bayar dengan uang tunai di tempat</span>
                                </div>
                            </div>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="qris">
                            <div class="option-info">
                                <i class="fas fa-qrcode" style="color: #16a34a;"></i>
                                <div>
                                    <strong>QRIS</strong>
                                    <span>Scan kode QRIS dengan aplikasi mobile banking</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="checkout-card">
                    <h3>Catatan (Opsional)</h3>
                    <textarea name="notes" placeholder="Contoh: Kurangi gula, tidak ada es, dll..." class="notes-textarea" maxlength="500"></textarea>
                </section>
            </div>

            <div class="checkout-summary">
                <div class="summary-card">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="order-items">
                        @foreach($cartItems as $item)
                            @php
                                $itemPrice = $item->price ?? ($item->unit_price ?? $item->menu->getPriceForTemperature($item->temperature ?? null));
                                $itemSubtotal = $itemPrice * $item->quantity;
                            @endphp
                            <div class="summary-item">
                                <div class="item-info">
                                    <span class="item-name" style="display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                        {{ $item->name ?? $item->menu->name }}
                                        @if(!empty($item->temperature))
                                            <span style="font-size: 10px; padding: 1px 6px; border-radius: 4px; font-weight: 700; font-family: monospace; {{ $item->temperature === 'Hot' ? 'background: #FEF3C7; color: #92400E;' : 'background: #E0F2FE; color: #075985;' }}">
                                                {{ $item->temperature === 'Hot' ? '🔥 Hot' : '🧊 Ice' }}
                                            </span>
                                        @endif
                                    </span>
                                    @if(!empty($item->note))
                                        <div style="font-size: 11px; color: #78716C; margin-top: 2px;">
                                            📝 {{ $item->note }}
                                        </div>
                                    @endif
                                    <span class="item-qty">x{{ $item->quantity }}</span>
                                </div>
                                <span class="item-total">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="divider">

                    <div class="summary-calculation">
                        <div class="calc-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="calc-row">
                            <span id="service-charge-label">Biaya layanan</span>
                            <span id="service-charge-amount">Rp {{ number_format($serviceCharge, 0, ',', '.') }}</span>
                        </div>
                        <div class="calc-row" id="discount-row">
                            <span id="discount-label">Diskon pagi 5%</span>
                            <span id="discount-amount">Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                        <div class="calc-row total">
                            <span>Total</span>
                            <span id="total-amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button class="btn-pay" onclick="openCustomerModal()">
                        <i class="fas fa-user"></i> Lanjut &amp; Isi Data Pelanggan
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- MODAL DATA PELANGGAN (wajib diisi sebelum checkout) --}}
<div id="customer-modal" 
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     role="dialog" aria-modal="true" aria-labelledby="customer-modal-title">
    <div class="bg-white rounded-lg w-full max-w-md shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-start justify-between gap-4">
            <div>
                <h3 id="customer-modal-title" class="text-lg font-bold text-gray-800">Data Pelanggan</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Mohon lengkapi data berikut sebelum menyelesaikan pesanan.
                </p>
            </div>
            <button type="button" onclick="closeCustomerModal()" aria-label="Tutup"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="customer-form" class="px-6 py-5 space-y-4" novalidate>
            <div>
                <label for="customer_table_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Meja
                </label>
                <input type="text"
                       name="table_id"
                       id="customer_table_id"
                       readonly
                       value="{{ $table->nama_meja ?? request('meja') ?? '' }}"
                       class="w-full px-3 py-2.5 border rounded-md text-sm bg-stone-100 text-stone-700 font-semibold cursor-not-allowed"
                       aria-describedby="table-help">
                <p id="table-help" class="text-[11px] text-stone-400 mt-1">
                    Meja terisi otomatis dari QR Code Anda dan tidak dapat diubah.
                </p>
            </div>

            <div>
                <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="customer_name" 
                       id="customer_name"
                       maxlength="255"
                       required
                       autocomplete="name"
                       placeholder="Contoh: Budi Santoso"
                       class="w-full px-3 py-2.5 border rounded-md text-sm focus:outline-none focus:border-[#bf4f08] focus:ring-1 focus:ring-[#bf4f08]">
                <p class="field-error text-xs text-red-600 mt-1 hidden"></p>
            </div>

            <div>
                <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nomor Telepon / WhatsApp <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <input type="tel" 
                       name="customer_phone" 
                       id="customer_phone"
                       maxlength="20"
                       inputmode="numeric"
                       autocomplete="tel"
                       placeholder="Contoh: 081234567890"
                       class="w-full px-3 py-2.5 border rounded-md text-sm focus:outline-none focus:border-[#bf4f08] focus:ring-1 focus:ring-[#bf4f08]">
                <p class="text-[11px] text-stone-400 mt-1">Isi dengan 9-15 digit angka bila ingin dihubungi via WhatsApp.</p>
                <p class="field-error text-xs text-red-600 mt-1 hidden"></p>
            </div>

            <div class="pt-2 space-y-2">
                <button type="submit" id="customer-submit" class="btn-pay !mb-0">
                    <i class="fas fa-check"></i> Konfirmasi &amp; Pesan
                </button>
                <button type="button" onclick="closeCustomerModal()" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .checkout-page {
        background: #f5f5f5;
        padding: 20px 0;
        min-height: calc(100vh - 100px);
    }

    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .checkout-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .checkout-header h1 {
        margin: 0 0 10px 0;
        font-size: 2em;
        color: #333;
    }

    .checkout-header p {
        margin: 0;
        color: #666;
    }

    .checkout-content {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 20px;
        margin-bottom: 40px;
    }

    @media (max-width: 768px) {
        .checkout-content {
            grid-template-columns: 1fr;
        }
    }

    .checkout-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .checkout-card h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #333;
    }

    .option-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .option-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .option-item:hover {
        border-color: #bf4f08;
        background: #fff9f5;
    }

    .option-item input[type="radio"] {
        margin-right: 12px;
        cursor: pointer;
        width: 18px;
        height: 18px;
        accent-color: #bf4f08;
    }

    .option-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .option-info i {
        font-size: 20px;
    }

    .option-info div strong {
        display: block;
        color: #333;
    }

    .option-info div span {
        font-size: 12px;
        color: #999;
    }

    .option-item input[type="radio"]:checked + .option-info div strong {
        color: #bf4f08;
    }

    .notes-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        resize: vertical;
        min-height: 80px;
    }

    .notes-textarea:focus {
        outline: none;
        border-color: #bf4f08;
    }

    .checkout-summary {
        background: white;
        border-radius: 8px;
        padding: 20px;
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .summary-card h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #333;
    }

    .order-items {
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .item-info {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .item-name {
        font-size: 14px;
        color: #333;
    }

    .item-qty {
        font-size: 12px;
        color: #999;
    }

    .item-note {
        font-size: 11px;
        color: #b7791f;
        margin-top: 2px;
    }

    .item-total {
        font-weight: bold;
        color: #bf4f08;
    }

    .divider {
        border: none;
        border-top: 2px solid #eee;
        margin: 15px 0;
    }

    .summary-calculation {
        margin-bottom: 20px;
    }

    .calc-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
        color: #666;
    }

    .calc-row.total {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #eee;
    }

    .btn-pay {
        width: 100%;
        padding: 12px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        margin-bottom: 10px;
    }

    .btn-pay:hover {
        background: #229954;
    }

    .btn-cancel {
        display: block;
        width: 100%;
        padding: 12px;
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: #e0e0e0;
    }

    /* Modal */
    #customer-modal {
        display: none;
    }

    #customer-modal.is-open {
        display: flex;
    }

    .field-error.is-visible {
        display: block;
    }
</style>

<script>
const cartItems = @json($cartItemsData ?? []);

function calculatePrice() {
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;
    
    let subtotal = 0;
    cartItems.forEach(item => {
        subtotal += item.menu.harga * item.quantity;
    });

    let serviceCharge = 0;
    if (serviceType === 'take_away') {
        const itemCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        serviceCharge = itemCount * 1000;
        document.getElementById('service-charge-label').textContent = `Biaya Take-away (${itemCount}x @ 1k)`;
    } else {
        document.getElementById('service-charge-label').textContent = 'Biaya layanan';
    }

    const currentHour = new Date().getHours();
    const discount = (currentHour >= 6 && currentHour < 11) ? Math.round(subtotal * 0.05) : 0;
    const total = subtotal + serviceCharge - discount;

    document.getElementById('service-charge-amount').textContent = 'Rp ' + number_format(serviceCharge, 0, ',', '.');

    const discountRow = document.getElementById('discount-row');
    if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('discount-label').textContent = 'Diskon pagi 5%';
        document.getElementById('discount-amount').textContent = 'Rp -' + number_format(discount, 0, ',', '.');
    } else {
        discountRow.style.display = 'none';
    }

    document.getElementById('total-amount').textContent = 'Rp ' + number_format(total, 0, ',', '.');
}

function number_format(number, decimals, dec_point, thousands_sep) {
    let n = !isFinite(+number) ? 0 : +number;
    let prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    let sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    let dec = (typeof dec_point === 'undefined') ? ',' : dec_point;
    let s = '';
    let toFixedFix = function(n, prec) {
        let k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/* --- Modal Data Pelanggan --- */
function openCustomerModal() {
    const modal = document.getElementById('customer-modal');
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    document.getElementById('customer_name').focus();
}

function closeCustomerModal() {
    const modal = document.getElementById('customer-modal');
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
}

function showFieldError(inputId, message) {
    const input = document.getElementById(inputId);
    const error = input.parentElement.querySelector('.field-error');
    input.classList.add('border-red-500');

    if (message) {
        error.textContent = message;
        error.classList.remove('hidden');
        error.classList.add('is-visible');
    } else {
        error.classList.add('hidden');
        error.classList.remove('is-visible');
        input.classList.remove('border-red-500');
    }
}

function clearFieldErrors() {
    showFieldError('customer_name', null);
    showFieldError('customer_phone', null);
}

function processPayment(form) {
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const notes = document.querySelector('textarea[name="notes"]').value;

    const btn = document.getElementById('customer-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    fetch('{{ route('order.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            service_type: serviceType,
            payment_method: paymentMethod,
            notes: notes,
            customer_name: form.customer_name.value.trim(),
            customer_phone: form.customer_phone.value.trim(),
            table_id: form.table_id.value.trim() || null
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (! response.ok) {
            if (data.errors && data.errors.customer_name) {
                showFieldError('customer_name', data.errors.customer_name[0]);
            }

            if (data.errors && data.errors.customer_phone) {
                showFieldError('customer_phone', data.errors.customer_phone[0]);
            }

            throw new Error(data.message || 'Gagal menyimpan pesanan.');
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Konfirmasi &amp; Pesan';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Terjadi kesalahan saat memproses pembayaran');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Konfirmasi &amp; Pesan';
    });
}

// Initialize price calculation and add event listeners
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();

    document.querySelectorAll('input[name="service_type"]').forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    const form = document.getElementById('customer-form');
    const nameInput = document.getElementById('customer_name');
    const phoneInput = document.getElementById('customer_phone');

    nameInput.addEventListener('input', () => showFieldError('customer_name', null));
    phoneInput.addEventListener('input', () => showFieldError('customer_phone', null));

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearFieldErrors();

        const nameVal = nameInput.value.trim();
        const phoneVal = phoneInput.value.trim();
        let valid = true;

        if (! nameVal) {
            showFieldError('customer_name', 'Nama lengkap wajib diisi.');
            valid = false;
        }

        // Telepon opsional — hanya divalidasi format bila diisi.
        if (phoneVal && ! /^[0-9]{9,15}$/.test(phoneVal)) {
            showFieldError('customer_phone', 'Nomor telepon harus terdiri dari 9-15 digit angka.');
            valid = false;
        }

        if (! valid) {
            return;
        }

        processPayment(form);
    });

    // Tutup modal dengan tombol Escape atau klik area luar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCustomerModal();
        }
    });

    document.getElementById('customer-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCustomerModal();
        }
    });
});
</script>
@endsection
