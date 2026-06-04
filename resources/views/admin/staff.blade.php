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

    .staff-container {
        background: linear-gradient(135deg, #FFFBF6 0%, #FFF8F0 100%);
        padding: 2.5rem;
        border-radius: 28px;
        box-shadow: 0 4px 20px rgba(107, 63, 31, 0.08);
    }

    .staff-header {
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid rgba(212, 165, 116, 0.2);
    }

    .staff-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--berco-dark-brown) 0%, var(--berco-light-brown) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .staff-header p {
        font-family: 'DM Sans', sans-serif;
        color: #8B7355;
        font-size: 1rem;
        font-weight: 500;
    }

    .btn-add-staff {
        background: linear-gradient(135deg, var(--berco-light-brown) 0%, var(--berco-brown) 100%);
        color: white;
        padding: 0.75rem 1.75rem;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all var(--transition-smooth);
        box-shadow: 0 4px 15px rgba(160, 104, 58, 0.3);
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-add-staff:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(160, 104, 58, 0.4);
        background: linear-gradient(135deg, var(--berco-brown) 0%, var(--berco-dark-brown) 100%);
    }

    /* Staff Table Styles */
    .staff-table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(107, 63, 31, 0.08);
        border: 1px solid rgba(212, 165, 116, 0.15);
    }

    .staff-table {
        width: 100%;
        border-collapse: collapse;
    }

    .staff-table thead {
        background: linear-gradient(90deg, rgba(212, 165, 116, 0.1) 0%, rgba(107, 63, 31, 0.08) 100%);
        border-bottom: 2px solid rgba(212, 165, 116, 0.2);
    }

    .staff-table thead th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--berco-dark-brown);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .staff-table tbody tr {
        border-bottom: 1px solid rgba(212, 165, 116, 0.1);
        transition: all var(--transition-smooth);
    }

    .staff-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(212, 165, 116, 0.05) 0%, rgba(160, 104, 58, 0.03) 100%);
        box-shadow: inset 0 0 0 1px rgba(212, 165, 116, 0.1);
    }

    .staff-table tbody td {
        padding: 1.25rem 1.5rem;
        color: #3D2817;
        font-family: 'DM Sans', sans-serif;
    }

    .staff-id {
        font-weight: 700;
        color: var(--berco-light-brown);
        font-size: 0.95rem;
    }

    .staff-name {
        font-weight: 600;
        color: var(--berco-dark-brown);
        font-size: 1rem;
    }

    .staff-username {
        font-family: 'JetBrains Mono', monospace;
        color: #8B7355;
        font-size: 0.9rem;
        background: rgba(212, 165, 116, 0.08);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    /* Role Badge */
    .role-badge {
        display: inline-block;
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        background: linear-gradient(135deg, rgba(107, 63, 31, 0.1) 0%, rgba(160, 104, 58, 0.08) 100%);
        color: var(--berco-dark-brown);
        border: 1px solid rgba(160, 104, 58, 0.3);
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid rgba(21, 87, 36, 0.2);
    }

    .status-badge.inactive {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border-color: rgba(114, 28, 36, 0.2);
    }

    /* Actions */
    .staff-actions {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
    }

    .action-btn {
        padding: 0.5rem;
        border-radius: 10px;
        transition: all var(--transition-smooth);
        border: none;
        cursor: pointer;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        text-decoration: none;
    }

    .action-btn.edit {
        color: #0066cc;
    }

    .action-btn.edit:hover {
        background: rgba(0, 102, 204, 0.1);
        color: #0052a3;
    }

    .action-btn.delete {
        color: #dc3545;
    }

    .action-btn.delete:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #c82333;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: rgba(212, 165, 116, 0.4);
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: #8B7355;
        font-size: 1.1rem;
        font-weight: 500;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.75rem;
        margin-top: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 18px;
        padding: 1.75rem;
        box-shadow: 0 2px 12px rgba(107, 63, 31, 0.08);
        border: 1px solid rgba(212, 165, 116, 0.15);
        transition: all var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--berco-light-brown), var(--berco-amber));
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(107, 63, 31, 0.15);
    }

    .stat-card.total::before {
        background: linear-gradient(90deg, #D4A574, #A0683A);
    }

    .stat-card.active::before {
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    .stat-card.roles::before {
        background: linear-gradient(90deg, #007bff, #0056b3);
    }

    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-family: 'DM Sans', sans-serif;
        color: #8B7355;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-family: 'Playfair Display', serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--berco-dark-brown);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .stat-card.total .stat-icon {
        background: linear-gradient(135deg, rgba(212, 165, 116, 0.2) 0%, rgba(160, 104, 58, 0.15) 100%);
        color: var(--berco-light-brown);
    }

    .stat-card.active .stat-icon {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, rgba(32, 201, 151, 0.15) 100%);
        color: #28a745;
    }

    .stat-card.roles .stat-icon {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.2) 0%, rgba(0, 86, 179, 0.15) 100%);
        color: #007bff;
    }

    @media (max-width: 980px) {
        .staff-container {
            padding: 1.5rem;
        }

        .staff-header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .staff-container {
            padding: 1rem;
            border-radius: 16px;
        }

        .staff-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }

        .staff-header h1 {
            font-size: 1.75rem;
        }

        .staff-table thead th {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .staff-table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }

        .staff-actions {
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.4rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

<div class="staff-container">
    <!-- Header -->
    <div class="staff-header flex justify-between items-start gap-6">
        <div class="flex-1">
            <h1>Staff Management</h1>
            <p>Manage and monitor all staff members</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn-add-staff">
            <i class="fas fa-user-plus"></i>
            <span>Add Staff</span>
        </a>
    </div>

    <!-- Staff Table -->
    <div class="staff-table-container">
        <div class="overflow-x-auto">
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $user)
                        <tr>
                            <td class="staff-id">#{{ $user->id_user }}</td>
                            <td class="staff-name">{{ $user->name }}</td>
                            <td class="staff-username">{{ $user->username }}</td>
                            <td>
                                <span class="role-badge">
                                    {{ $user->role->role_name ?? 'Staff' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge">
                                    <i class="fas fa-check-circle" style="margin-right: 0.35rem;"></i>Active
                                </span>
                            </td>
                            <td>
                                <div class="staff-actions">
                                    <a href="{{ route('admin.staff.edit', $user->id_user) }}" 
                                       class="action-btn edit" title="Edit Staff">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.staff.delete', $user->id_user) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" 
                                                title="Delete Staff" onclick="return confirm('Are you sure you want to delete this staff member?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <p class="empty-state-text">No staff members found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Total Staff</p>
                    <p class="stat-value">{{ count($staff) }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="stat-card active">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Active Now</p>
                    <p class="stat-value">{{ count($staff) }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stat-card roles">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Total Roles</p>
                    <p class="stat-value">{{ count($staff->groupBy('role_id') ?? []) }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection