@extends('dashboard')

@push('styles')
<style>
    :root {
        --berco-brown: #6B3F1F;
        --berco-amber: #D4A574;
        --berco-cream: #FFF8F0;
        --berco-dark-brown: #4A2C1F;
        --berco-light-brown: #A0683A;
        --transition-smooth: 0.3s ease;
    }

    .edit-staff-container {
        background: linear-gradient(135deg, #FFFBF6 0%, #FFF8F0 100%);
        padding: 2.5rem;
        border-radius: 28px;
        box-shadow: 0 4px 20px rgba(107, 63, 31, 0.08);
        max-width: 700px;
        margin: 0 auto;
    }

    .edit-staff-header {
        margin-bottom: 2.5rem;
        text-align: center;
        padding-bottom: 2rem;
        border-bottom: 2px solid rgba(212, 165, 116, 0.2);
    }

    .edit-staff-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--berco-dark-brown) 0%, var(--berco-light-brown) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.5px;
    }

    .edit-staff-header .subtitle {
        color: #8B7355;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .form-card {
        background: white;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(107, 63, 31, 0.08);
        border: 1px solid rgba(212, 165, 116, 0.15);
        margin-bottom: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .form-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--berco-dark-brown);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(212, 165, 116, 0.15);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-section-title::before {
        content: '';
        width: 4px;
        height: 1.2rem;
        background: linear-gradient(180deg, var(--berco-light-brown) 0%, var(--berco-amber) 100%);
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        color: var(--berco-dark-brown);
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label .label-note {
        font-weight: 500;
        color: #8B7355;
        font-size: 0.85rem;
    }

    .form-control {
        font-family: 'DM Sans', sans-serif;
        padding: 0.75rem 1rem !important;
        border: 2px solid rgba(212, 165, 116, 0.3) !important;
        border-radius: 10px !important;
        font-size: 0.95rem;
        transition: all var(--transition-smooth);
        background: white !important;
        color: #3D2817 !important;
    }

    .form-control:focus {
        border-color: var(--berco-light-brown) !important;
        box-shadow: 0 0 0 3px rgba(160, 104, 58, 0.1) !important;
        background: white !important;
    }

    .form-control::placeholder {
        color: #C0A080;
    }

    .form-control.is-invalid {
        border-color: #dc3545 !important;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
    }

    .invalid-feedback {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: 0.5rem;
        display: block;
        font-weight: 500;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%) !important;
        border: 2px solid rgba(220, 53, 69, 0.3) !important;
        border-radius: 12px !important;
        padding: 1.25rem 1.5rem !important;
        color: #721c24 !important;
        margin-bottom: 2rem !important;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
    }

    .alert-danger strong {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .alert-danger ul {
        margin: 0.5rem 0 0 1.25rem;
        padding-left: 0;
    }

    .alert-danger li {
        margin-bottom: 0.25rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid rgba(212, 165, 116, 0.15);
    }

    .btn-submit {
        flex: 1;
        background: linear-gradient(135deg, var(--berco-light-brown) 0%, var(--berco-brown) 100%);
        color: white !important;
        padding: 0.95rem 1.75rem !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        border: none !important;
        cursor: pointer !important;
        transition: all var(--transition-smooth);
        box-shadow: 0 4px 15px rgba(160, 104, 58, 0.3) !important;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem !important;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(160, 104, 58, 0.4) !important;
        background: linear-gradient(135deg, var(--berco-brown) 0%, var(--berco-dark-brown) 100%) !important;
        color: white !important;
    }

    .btn-cancel {
        flex: 1;
        background: white !important;
        color: var(--berco-light-brown) !important;
        padding: 0.95rem 1.75rem !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        border: 2px solid var(--berco-light-brown) !important;
        cursor: pointer !important;
        transition: all var(--transition-smooth);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem !important;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-cancel:hover {
        background: linear-gradient(135deg, rgba(160, 104, 58, 0.1) 0%, rgba(107, 63, 31, 0.08) 100%) !important;
        color: var(--berco-brown) !important;
    }

    .form-info-box {
        background: linear-gradient(135deg, rgba(212, 165, 116, 0.1) 0%, rgba(160, 104, 58, 0.05) 100%);
        border-left: 4px solid var(--berco-light-brown);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #6B3F1F;
        font-weight: 500;
    }

    .form-info-box strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--berco-dark-brown);
    }

    @media (max-width: 768px) {
        .edit-staff-container {
            padding: 1.5rem;
        }

        .form-card {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit,
        .btn-cancel {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="edit-staff-container">
    <div class="edit-staff-header">
        <h2>✏️ Edit Staff</h2>
        <p class="subtitle">{{ $staff->name }}</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>⚠️ Terjadi Kesalahan</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('admin.staff.update', $staff->id_user) }}">
            @csrf
            @method('PUT')

            <!-- Informasi Dasar -->
            <div class="form-section">
                <div class="form-section-title">👤 Informasi Dasar</div>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $staff->name) }}" 
                           placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                           id="username" name="username" value="{{ old('username', $staff->username) }}" 
                           placeholder="Masukkan username" required>
                    @error('username')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="id_role" class="form-label">Role / Posisi</label>
                    <select class="form-control @error('id_role') is-invalid @enderror" 
                            id="id_role" name="id_role" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            @if(in_array($role->role_name, ['Admin', 'Staff']))
                                <option value="{{ $role->id_role }}" 
                                        {{ old('id_role', $staff->id_role) == $role->id_role ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('id_role')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Keamanan -->
            <div class="form-section">
                <div class="form-section-title">🔐 Keamanan</div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password
                        <span class="label-note">(Opsional - biarkan kosong jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" 
                           placeholder="Masukkan password baru">
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" 
                           placeholder="Ulangi password baru">
                </div>

                <div class="form-info-box">
                    <strong>💡 Tips:</strong>
                    Password harus minimal 8 karakter dan kuat
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    ✅ Update Staff
                </button>
                <a href="{{ route('admin.staffoption.index') }}" class="btn-cancel">
                    ❌ Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
