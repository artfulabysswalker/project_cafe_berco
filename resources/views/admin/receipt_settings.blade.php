@extends('dashboard')

@section('page-title', 'Edit Struk')
@section('breadcrumb', 'Receipt Settings')

@section('content')

<!-- Form and Preview -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <!-- Form Section -->
    <div>
        <div class="receipt-form">
            <div class="form-row">
                <label class="form-label">Nama Toko</label>
                <input type="text" class="form-input" value="BERCO CAFE" />
            </div>
            <div class="form-row">
                <label class="form-label">Alamat</label>
                <textarea class="form-input" rows="2">Jl. Merdeka No. 123, Kota, 12345</textarea>
            </div>
            <div class="form-row">
                <label class="form-label">Nomor Telepon</label>
                <input type="tel" class="form-input" value="(021) 1234-5678" />
            </div>
            <div class="form-row">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="info@bercocafe.com" />
            </div>
            <div class="form-row">
                <label class="form-label">Website</label>
                <input type="text" class="form-input" value="www.bercocafe.com" />
            </div>
            <div class="form-row">
                <label class="form-label">Catatan Struk (Terima Kasih)</label>
                <textarea class="form-input" rows="2">Terima kasih atas kunjungan Anda!
Silakan berkunjung kembali.</textarea>
            </div>
            <div class="form-row">
                <label class="form-label">Atur Pajak (%)</label>
                <input type="number" class="form-input" value="10" />
            </div>
            <div class="form-row">
                <label class="form-label">Format Tanggal</label>
                <select class="form-input">
                    <option>DD/MM/YYYY HH:MM</option>
                    <option>MM/DD/YYYY HH:MM</option>
                    <option>YYYY-MM-DD HH:MM</option>
                </select>
            </div>
            <button class="save-btn"><i class="ti ti-save"></i> Simpan Perubahan</button>
        </div>
    </div>

    <!-- Preview Section -->
    <div>
        <div style="font-size:13px;font-weight:600;color:var(--color-text-primary);margin-bottom:10px;display:flex;align-items:center;gap:6px">
            <i class="ti ti-eye" style="color:#D4752C"></i> Preview Struk
        </div>
        <div class="receipt-preview">
            <div style="font-size:13px;font-weight:700;color:#1a1a1a">BERCO CAFE</div>
            <div style="font-size:10px;color:#666;margin-top:4px">Jl. Merdeka No. 123</div>
            <div style="font-size:10px;color:#666">Kota, 12345</div>
            <div style="font-size:10px;color:#666;margin-top:4px">(021) 1234-5678</div>
            <div style="font-size:10px;color:#666">info@bercocafe.com</div>
            
            <div style="border-top:1px dashed #ccc;margin:10px 0;padding-top:8px;font-size:10px;text-align:center">
                <div style="font-weight:600">STRUK PEMBELIAN</div>
            </div>

            <div style="font-size:9px;margin:6px 0">
                <div style="display:flex;justify-content:space-between">
                    <span>No. Pesanan:</span>
                    <span>#2</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>Tanggal:</span>
                    <span>02/06/2026 11:53</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>Kasir:</span>
                    <span>Admin</span>
                </div>
            </div>

            <div style="border-top:1px dashed #ccc;margin:8px 0;padding-top:6px">
                <div style="display:flex;justify-content:space-between;font-size:9px;margin-bottom:4px">
                    <span style="flex:1">Item</span>
                    <span style="width:30px;text-align:right">Qty</span>
                    <span style="width:45px;text-align:right">Rp</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:9px">
                    <span style="flex:1">Cappuccino</span>
                    <span style="width:30px;text-align:right">2</span>
                    <span style="width:45px;text-align:right">36k</span>
                </div>
            </div>

            <div style="border-top:1px dashed #ccc;margin:8px 0;padding-top:6px">
                <div style="display:flex;justify-content:space-between;font-size:9px">
                    <span>Subtotal:</span>
                    <span>Rp 36.000</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:9px">
                    <span>Pajak (10%):</span>
                    <span>Rp 3.600</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:10px;font-weight:600;margin-top:4px">
                    <span>TOTAL:</span>
                    <span>Rp 39.600</span>
                </div>
            </div>

            <div style="border-top:1px dashed #ccc;margin:8px 0;padding-top:6px;font-size:9px">
                <div style="display:flex;justify-content:space-between">
                    <span>Metode Bayar:</span>
                    <span>Cash</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>Status:</span>
                    <span>Lunas</span>
                </div>
            </div>

            <div style="border-top:1px dashed #ccc;margin:10px 0;padding-top:8px;font-size:9px;text-align:center;color:#666">
                Terima kasih atas kunjungan Anda!<br/>
                Silakan berkunjung kembali.
            </div>

            <div style="text-align:center;font-size:8px;color:#999;margin-top:8px">
                www.bercocafe.com
            </div>
        </div>
    </div>
</div>

<style>
    .receipt-form {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 14px;
    }
    .form-row {
        margin-bottom: 12px;
    }
    .form-label {
        font-size: 10px;
        color: var(--color-text-secondary);
        margin-bottom: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: block;
    }
    .form-input {
        width: 100%;
        padding: 8px 10px;
        border-radius: 7px;
        border: 0.5px solid var(--color-border-tertiary);
        font-size: 12px;
        background: var(--color-background-secondary);
        color: var(--color-text-primary);
        font-family: inherit;
    }
    .form-input:focus {
        outline: none;
        border-color: #D4752C;
        background: #fff;
    }
    .save-btn {
        width: 100%;
        padding: 8px 12px;
        border-radius: 7px;
        font-size: 12px;
        background: #D4752C;
        color: #fff;
        border: none;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 6px;
    }
    .save-btn:hover {
        background: #c26620;
    }
    .receipt-preview {
        background: #F9F5F0;
        border: 0.5px dashed var(--color-border-tertiary);
        border-radius: 9px;
        padding: 14px;
        font-family: 'Courier New', monospace;
        text-align: center;
    }
</style>

@endsection
