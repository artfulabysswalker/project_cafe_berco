@extends('dashboard')

@section('page-title', 'Tambah Produk Menu')
@section('breadcrumb', 'Menu / Tambah Produk')

@section('content')
<div class="create-product-page">

    {{-- Error Alerts --}}
    @if(isset($errors) && $errors->any())
        <div class="alert-danger-custom">
            <i class="fas fa-triangle-exclamation"></i>
            <ul style="margin:0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container-card">
        <div class="form-header">
            <div>
                <h2 class="form-title"><i class="fas fa-plus-circle text-amber"></i> Tambah Produk / Menu Baru</h2>
                <p class="form-subtitle">Lengkapi informasi detail produk, kategori, penetapan harga jual, serta stok ketersediaan.</p>
            </div>
            <a href="{{ route('admin.menu') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>

        <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data" class="form-content">
            @csrf

            <div class="form-grid-layout">
                {{-- Left Column: Basic Info & Categories --}}
                <div class="form-column">
                    {{-- 1. Nama Produk --}}
                    <div class="form-group">
                        <label class="form-label" for="nama_menu">
                            Nama Produk / Menu <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama_menu" name="nama_menu" value="{{ old('nama_menu') }}" class="form-input" placeholder="Contoh: Kopi Susu Aren Gula Aren, Croissant..." required>
                    </div>

                    {{-- 2. Kategori Produk with Quick Add Button --}}
                    <div class="form-group">
                        <div class="label-row-with-action">
                            <label class="form-label" for="id_kategori">
                                Kategori Produk <span class="text-danger">*</span>
                            </label>
                            <button type="button" class="btn-inline-add" onclick="openCategoryModal()">
                                <i class="fas fa-plus"></i> Tambah Kategori Baru
                            </button>
                        </div>
                        <select id="id_kategori" name="id_kategori" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('id_kategori') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Pricing: Harga Jual --}}
                    <div class="form-group">
                        <label class="form-label" for="harga">
                            Harga Jual (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">Rp</span>
                            <input type="number" id="harga" name="harga" value="{{ old('harga') }}" class="form-input with-prefix" placeholder="25000" min="0" required>
                        </div>
                        <p class="text-[11px] text-stone-500 mt-1.5 flex items-center gap-1.5">
                            <i class="fas fa-info-circle text-[#B45309]"></i> HPP modal produk akan otomatis dihitung dari resep bahan baku di menu <a href="{{ route('admin.hpp') }}" target="_blank" class="text-[#B45309] font-medium underline">HPP & Resep</a> setelah produk dibuat.
                        </p>
                    </div>

                    {{-- 4. Stok Produk & Status Aktif --}}
                    <div class="stock-status-row">
                        <div class="form-group flex-1">
                            <label class="form-label" for="stok">
                                Stok Awal (Unit/Porsi) <span class="text-danger">*</span>
                            </label>
                            <input type="number" id="stok" name="stok" value="{{ old('stok', 50) }}" class="form-input" min="0" required>
                        </div>

                        <div class="form-group flex-1">
                            <label class="form-label">Status Aktif Produk</label>
                            <label class="toggle-switch-card">
                                <input type="checkbox" name="status_tersedia" value="1" checked id="status_tersedia">
                                <span class="slider"></span>
                                <span class="toggle-label" id="statusLabel">Tersedia / Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Image Upload & Description --}}
                <div class="form-column">
                    {{-- 5. Upload Foto Produk --}}
                    <div class="form-group">
                        <label class="form-label">Foto Produk <span class="text-muted">(PNG, JPG, Max 2MB)</span></label>
                        <div class="image-upload-dropzone" onclick="document.getElementById('foto').click()">
                            <input type="file" id="foto" name="foto" accept="image/*" style="display:none;" onchange="previewImage(event)">
                            <div id="imagePreviewContainer" style="display:none;" class="image-preview-wrap">
                                <img id="imagePreview" src="" alt="Preview">
                                <span class="change-photo-btn"><i class="fas fa-camera"></i> Ganti Foto</span>
                            </div>
                            <div id="uploadPlaceholder" class="upload-placeholder">
                                <i class="fas fa-cloud-arrow-up upload-icon"></i>
                                <strong>Klik untuk unggah foto produk</strong>
                                <span>Format PNG, JPG, JPEG atau WebP</span>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Deskripsi Produk --}}
                    <div class="form-group">
                        <label class="form-label" for="deskripsi">Deskripsi Singkat Produk</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-textarea" rows="4" placeholder="Jelaskan cita rasa, komposisi, atau catatan khusus menu ini...">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Form Footer Actions --}}
            <div class="form-footer">
                <a href="{{ route('admin.menu') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i> Simpan Produk Menu
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TAMBAH KATEGORI BARU VIA AJAX --}}
<div id="categoryModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-folder-plus text-amber"></i> Tambah Kategori Baru</h3>
            <button type="button" class="btn-close-modal" onclick="closeCategoryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="categoryErrorAlert" style="display:none;" class="alert-danger-custom mb-3"></div>

            <div class="form-group">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" id="modal_nama_kategori" class="form-input" placeholder="Contoh: Kopi Spesial, Snack, Pastry..." required>
            </div>

            <div class="form-group" style="margin-top: 14px;">
                <label class="form-label">Icon Kategori</label>
                <select id="modal_icon" class="form-select">
                    <option value="fas fa-coffee">☕ Coffee (fas fa-coffee)</option>
                    <option value="fas fa-mug-hot">🍵 Hot Drinks (fas fa-mug-hot)</option>
                    <option value="fas fa-glass-water">🥤 Cold Drinks (fas fa-glass-water)</option>
                    <option value="fas fa-burger">🍔 Food / Burger (fas fa-burger)</option>
                    <option value="fas fa-cookie-bite">🍪 Pastry / Snack (fas fa-cookie-bite)</option>
                    <option value="fas fa-blender">🍧 Ice Blended (fas fa-blender)</option>
                    <option value="fas fa-tag">🏷️ Tag Default (fas fa-tag)</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeCategoryModal()">Batal</button>
            <button type="button" class="btn-submit" onclick="submitAjaxCategory()">
                <i class="fas fa-plus"></i> Tambahkan
            </button>
        </div>
    </div>
</div>

<style>
    .create-product-page {
        width: 100%;
        max-width: 100%;
        margin: 0;
    }

    .alert-danger-custom {
        background: #fef2f2;
        border: 1px solid #fecdd3;
        color: #9f1239;
        padding: 14px 20px;
        border-radius: 16px;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-container-card {
        background: #ffffff;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 24px;
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .form-header {
        padding: 24px 32px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .form-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-subtitle {
        color: #64748b;
        font-size: 13px;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        color: #475569;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-back:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .form-content {
        padding: 32px;
    }

    .form-grid-layout {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 32px;
    }

    .form-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .label-row-with-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .btn-inline-add {
        background: none;
        border: none;
        color: #D4752C;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0;
    }

    .btn-inline-add:hover {
        color: #b45309;
        text-decoration: underline;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        font-size: 14px;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #D4752C;
        box-shadow: 0 0 0 3px rgba(212, 117, 44, 0.12);
    }

    .pricing-row, .stock-status-row {
        display: flex;
        gap: 16px;
    }

    .flex-1 { flex: 1; }

    .input-prefix-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-prefix {
        position: absolute;
        left: 14px;
        font-weight: 700;
        color: #94a3b8;
        font-size: 14px;
    }

    .form-input.with-prefix {
        padding-left: 42px;
    }

    /* Margin Preview Box */
    .margin-preview-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .margin-item {
        display: flex;
        flex-direction: column;
    }

    .margin-lbl {
        font-size: 11px;
        color: #166534;
        font-weight: 600;
    }

    .margin-val {
        font-size: 16px;
        font-weight: 800;
    }

    .text-green { color: #15803d !important; }

    /* Toggle Switch */
    .toggle-switch-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        cursor: pointer;
        height: 48px;
    }

    .toggle-switch-card input {
        display: none;
    }

    .slider {
        width: 40px;
        height: 22px;
        background: #cbd5e1;
        border-radius: 999px;
        position: relative;
        transition: background 0.2s ease;
        flex-shrink: 0;
    }

    .slider:before {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        top: 3px;
        left: 3px;
        transition: transform 0.2s ease;
    }

    .toggle-switch-card input:checked + .slider {
        background: #10b981;
    }

    .toggle-switch-card input:checked + .slider:before {
        transform: translateX(18px);
    }

    .toggle-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    /* Image Dropzone */
    .image-upload-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 18px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        background: #f8fafc;
        transition: all 0.2s ease;
        min-height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-upload-dropzone:hover {
        border-color: #D4752C;
        background: #fffaf5;
    }

    .upload-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .upload-icon {
        font-size: 36px;
        color: #D4752C;
        margin-bottom: 4px;
    }

    .upload-placeholder strong {
        font-size: 13px;
        color: #1e293b;
    }

    .upload-placeholder span {
        font-size: 11px;
        color: #94a3b8;
    }

    .image-preview-wrap {
        width: 100%;
        height: 160px;
        position: relative;
        border-radius: 12px;
        overflow: hidden;
    }

    .image-preview-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .change-photo-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
    }

    /* Footer */
    .form-footer {
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
    }

    .btn-cancel {
        padding: 12px 24px;
        border-radius: 14px;
        background: #f1f5f9;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 14px;
        background: linear-gradient(135deg, #D4752C 0%, #B45309 100%);
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(212, 117, 44, 0.3);
    }

    .btn-submit:hover {
        box-shadow: 0 6px 20px rgba(212, 117, 44, 0.4);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #94a3b8;
        cursor: pointer;
    }

    .modal-body {
        padding: 22px 24px;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }

    @media (max-width: 860px) {
        .form-grid-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            document.getElementById('uploadPlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}


function openCategoryModal() {
    document.getElementById('categoryModal').style.display = 'flex';
    document.getElementById('modal_nama_kategori').focus();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

function submitAjaxCategory() {
    const namaKategori = document.getElementById('modal_nama_kategori').value.trim();
    const icon = document.getElementById('modal_icon').value;
    const errorAlert = document.getElementById('categoryErrorAlert');

    if (!namaKategori) {
        errorAlert.innerText = 'Nama kategori wajib diisi!';
        errorAlert.style.display = 'block';
        return;
    }

    fetch('{{ route("admin.categories.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            nama_kategori: namaKategori,
            icon: icon
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.category) {
            // Append and select the new category in dropdown
            const select = document.getElementById('id_kategori');
            const newOption = document.createElement('option');
            newOption.value = data.category.id;
            newOption.text = data.category.nama_kategori;
            newOption.selected = true;
            select.appendChild(newOption);

            closeCategoryModal();
            document.getElementById('modal_nama_kategori').value = '';
            errorAlert.style.display = 'none';
        } else {
            errorAlert.innerText = data.message || 'Gagal menambahkan kategori';
            errorAlert.style.display = 'block';
        }
    })
    .catch(err => {
        errorAlert.innerText = 'Kategori sudah ada atau terjadi kesalahan jaringan.';
        errorAlert.style.display = 'block';
    });
}

// Update status label dynamically
document.getElementById('status_tersedia').addEventListener('change', function() {
    document.getElementById('statusLabel').innerText = this.checked ? 'Tersedia / Aktif' : 'Habis / Nonaktif';
});
</script>
@endsection