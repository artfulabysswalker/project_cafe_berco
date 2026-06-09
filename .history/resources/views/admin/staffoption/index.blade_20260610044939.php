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

    .staff-management-container {
        background: linear-gradient(135deg, #FFFBF6 0%, #FFF8F0 100%);
        padding: 2.5rem;
        border-radius: 28px;
        box-shadow: 0 4px 20px rgba(107, 63, 31, 0.08);
    }

    .staff-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid rgba(212, 165, 116, 0.2);
    }

    .staff-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--berco-dark-brown) 0%, var(--berco-light-brown) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .btn-add-staff {
        background: linear-gradient(135deg, var(--berco-light-brown) 0%, var(--berco-brown) 100%);
        color: white !important;
        padding: 0.75rem 1.75rem !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        border: none !important;
        transition: all var(--transition-smooth) !important;
        box-shadow: 0 4px 15px rgba(160, 104, 58, 0.3) !important;
        text-decoration: none !important;
    }

    .btn-add-staff:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 25px rgba(160, 104, 58, 0.4) !important;
        background: linear-gradient(135deg, var(--berco-brown) 0%, var(--berco-dark-brown) 100%) !important;
        color: white !important;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%) !important;
        border: 1px solid rgba(21, 87, 36, 0.2) !important;
        color: #155724 !important;
        border-radius: 12px !important;
        padding: 1rem 1.5rem !important;
        font-weight: 500 !important;
    }

    .section-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(107, 63, 31, 0.08);
        border: 1px solid rgba(212, 165, 116, 0.15);
        margin-bottom: 2rem;
        transition: all var(--transition-smooth);
    }

    .section-card:hover {
        box-shadow: 0 4px 20px rgba(107, 63, 31, 0.12);
    }

    .section-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 2px solid rgba(212, 165, 116, 0.15);
    }

    .section-header h5 {
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
    }

    .section-card.admins .section-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .section-card.staffs .section-header {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    }

    .section-card.customers .section-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .section-body {
        padding: 1.75rem;
    }

    .empty-message {
        color: #8B7355;
        font-size: 1rem;
        font-weight: 500;
        padding: 1rem 0;
    }

    .table-responsive table {
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .table thead th {
        background: rgba(212, 165, 116, 0.08) !important;
        border-bottom: 2px solid rgba(212, 165, 116, 0.2) !important;
        padding: 1rem 1.25rem !important;
        font-family: 'DM Sans', sans-serif;
        font-weight: 700 !important;
        color: var(--berco-dark-brown) !important;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .table tbody td {
        padding: 1rem 1.25rem !important;
        border-bottom: 1px solid rgba(212, 165, 116, 0.1) !important;
        color: #3D2817;
        font-family: 'DM Sans', sans-serif;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all var(--transition-smooth);
    }

    .table tbody tr:hover {
        background: linear-gradient(90deg, rgba(212, 165, 116, 0.05) 0%, rgba(160, 104, 58, 0.03) 100%) !important;
        box-shadow: inset 0 0 0 1px rgba(212, 165, 116, 0.1);
    }

    .table tbody tr td:first-child {
        font-weight: 600;
        color: var(--berco-dark-brown);
    }

    .staff-username {
        font-family: 'JetBrains Mono', monospace;
        color: #8B7355;
        background: rgba(212, 165, 116, 0.08);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .badge {
        font-weight: 700 !important;
        padding: 0.4rem 0.75rem !important;
        border-radius: 20px !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.3px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .badge.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%) !important;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    /* Action Buttons */
    .btn-sm {
        padding: 0.5rem 0.75rem !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        transition: all var(--transition-smooth) !important;
        border: none !important;
        font-family: 'DM Sans', sans-serif;
    }

    .btn-warning {
        background: #0066cc !important;
        color: white !important;
    }

    .btn-warning:hover {
        background: #0052a3 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 86, 179, 0.3);
    }

    .btn-danger {
        background: #dc3545 !important;
        color: white !important;
    }

    .btn-danger:hover {
        background: #c82333 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    .btn-primary {
        background: var(--berco-light-brown) !important;
        color: white !important;
    }

    .btn-primary:hover {
        background: var(--berco-dark-brown) !important;
        transform: translateY(-1px);
    }

    .form-select {
        border: 1px solid rgba(212, 165, 116, 0.3) !important;
        border-radius: 8px !important;
        padding: 0.4rem 0.75rem !important;
        font-family: 'DM Sans', sans-serif !important;
        transition: all var(--transition-smooth) !important;
    }

    .form-select:focus {
        border-color: var(--berco-light-brown) !important;
        box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1) !important;
    }

    @media (max-width: 980px) {
        .staff-management-container {
            padding: 1.5rem;
        }

        .staff-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .staff-header h2 {
            font-size: 2rem;
        }

        .btn-add-staff {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 640px) {
        .staff-management-container {
            padding: 1rem;
            border-radius: 16px;
        }

        .staff-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }

        .staff-header h2 {
            font-size: 1.75rem;
        }

        .table thead th {
            padding: 0.75rem 0.5rem !important;
            font-size: 0.8rem;
        }

        .table tbody td {
            padding: 0.75rem 0.5rem !important;
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.4rem 0.6rem !important;
            font-size: 0.8rem !important;
        }

        .form-select {
            max-width: 120px !important;
        }
    }
</style>
@endpush

@section('content')
    <div class="staff-header">
        <h2>Manajemen Staff</h2>
        <a href="{{ route('admin.staffoption.create') }}" class="btn btn-add-staff">
            <i class="fas fa-plus"></i> Tambah Staff Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Admins Section -->
    <div class="section-card admins">
        <div class="section-header">
            <h5><i class="fas fa-crown"></i> Admin</h5>
        </div>
        <div class="section-body">
            @if($admins->isEmpty())
                <p class="empty-message"><i class="fas fa-user-slash"></i> Tidak ada admin</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th style="text-align: center; width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $person)
                                <tr>
                                    <td>{{ $person->name }}</td>
                                    <td><span class="staff-username">{{ $person->username }}</span></td>
                                    <td><span class="badge bg-danger"><i class="fas fa-star"></i> Admin</span></td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.staffoption.edit', $person->id_user) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if($person->id_user !== auth()->id())
                                            <form method="POST" 
                                                  action="{{ route('admin.staff.destroy', $person->id_user) }}" 
                                                  style="display:inline;" 
                                                  onsubmit="return confirm('Yakin hapus admin ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Staff Section -->
    <div class="section-card staffs">
        <div class="section-header">
            <h5><i class="fas fa-user-tie"></i> Staff / Kasir</h5>
        </div>
        <div class="section-body">
            @if($staffs->isEmpty())
                <p class="empty-message"><i class="fas fa-user-slash"></i> Tidak ada staff</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th style="text-align: center; width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffs as $person)
                                <tr>
                                    <td>{{ $person->name }}</td>
                                    <td><span class="staff-username">{{ $person->username }}</span></td>
                                    <td><span class="badge bg-info"><i class="fas fa-user-check"></i> Staff</span></td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.staffoption.edit', $person->id_user) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.staff.destroy', $person->id_user) }}" 
                                              style="display:inline;" 
                                              onsubmit="return confirm('Yakin hapus staff ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Customers Section -->
    <div class="section-card customers">
        <div class="section-header">
            <h5><i class="fas fa-user"></i> Customer</h5>
        </div>
        <div class="section-body">
            @if($users->isEmpty())
                <p class="empty-message"><i class="fas fa-user-slash"></i> Tidak ada customer</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th style="text-align: center; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $person)
                                <tr>
                                    <td>{{ $person->name }}</td>
                                    <td><span class="staff-username">{{ $person->username }}</span></td>
                                    <td><span class="staff-username">{{ $person->email }}</span></td>
                                    <td><span class="badge bg-success"><i class="fas fa-user-circle"></i> Customer</span></td>
                                    <td style="text-align: center;">
                                        <form method="POST" 
                                              action="{{ route('admin.staff.role', $person->id_user) }}" 
                                              style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <select name="id_role" class="form-select form-select-sm" 
                                                    style="max-width: 150px; display:inline-block;">
                                                @foreach($roles as $role)
                                                    @if(in_array($role->role_name, ['Customer', 'Staff', 'Admin']))
                                                        <option value="{{ $role->id_role }}" 
                                                                {{ $person->id_role == $role->id_role ? 'selected' : '' }}>
                                                            {{ $role->role_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                Ubah
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection