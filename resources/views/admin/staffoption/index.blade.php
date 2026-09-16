@extends('dashboard')

@section('page-title', 'Manajemen Staff & Karyawan')
@section('breadcrumb', 'Staff Directory')

@section('content')
<div class="space-y-5">

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-mono flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs font-mono flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-circle-exclamation text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
        </div>
    @endif

    {{-- Non-Admin Notice Banner --}}
    @if(!$isAdmin)
        <div class="bg-amber-50/60 border border-amber-200 rounded-lg p-3.5 flex items-center justify-between text-xs text-amber-900">
            <div class="flex items-center space-x-2.5">
                <i class="fas fa-shield-halved text-amber-600 text-sm"></i>
                <span>Anda sedang login sebagai <strong>{{ auth()->user()?->name ?? 'Staff' }}</strong> (Mode Direktori). Penambahan atau pengubahan data pengguna dibatasi untuk Administrator.</span>
            </div>
            <span class="text-[10px] font-mono uppercase bg-amber-100 px-2 py-0.5 rounded border border-amber-300 font-semibold">Lihat Saja</span>
        </div>
    @endif

    @php
        $allUsers = $admins->concat($staffs)->concat($users);
    @endphp

    {{-- SINGLE UNIFIED CARD CONTAINER --}}
    <div class="bg-white border border-neutral-200 rounded-xl shadow-xs overflow-hidden">
        
        {{-- 1. Header Area --}}
        <div class="p-5 border-b border-neutral-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2.5">
                    <h2 class="text-sm font-semibold text-neutral-900 tracking-tight">Manajemen Staff & Pengguna</h2>
                    <span class="text-[11px] font-mono text-neutral-600 bg-neutral-100 border border-neutral-200 px-2 py-0.5 rounded-full" id="userCountBadge">
                        {{ $allUsers->count() }} Akun
                    </span>
                </div>
                <p class="text-xs text-neutral-500 mt-0.5">Kelola hak akses peran, status keaktifan, dan kredensial akun tim operasional & pelanggan.</p>
            </div>

            <div class="flex items-center space-x-3">
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        id="staffSearchInput" 
                        placeholder="Cari nama, username, email..." 
                        onkeyup="applyStaffFilters()"
                        class="w-full bg-neutral-50 border border-neutral-200 rounded-md pl-8 pr-3 py-1.5 text-xs text-neutral-900 placeholder:text-neutral-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 transition-colors"
                    />
                    <i class="fas fa-magnifying-glass text-neutral-400 text-xs absolute left-2.5 top-2.5"></i>
                </div>

                @if($isAdmin)
                    <a href="{{ route('admin.staffoption.create') }}" class="text-xs font-mono font-medium bg-[#18181B] hover:bg-black text-white px-3.5 py-1.5 rounded-md transition-colors shadow-2xs inline-flex items-center space-x-1.5 shrink-0">
                        <i class="fas fa-plus text-[10px]"></i>
                        <span>Tambah Pegawai</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- 2. Filter Bar (Segmented Role Tabs + Status Dropdown) --}}
        <div class="px-5 py-3 border-b border-neutral-200 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            {{-- Role Filter Tabs --}}
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 sm:pb-0" id="roleTabGroup">
                <button 
                    type="button" 
                    onclick="selectRoleTab('all', this)" 
                    class="role-tab px-3 py-1 rounded-md font-medium text-xs bg-white text-neutral-900 border border-neutral-200 shadow-2xs transition-colors">
                    Semua ({{ $allUsers->count() }})
                </button>
                <button 
                    type="button" 
                    onclick="selectRoleTab('admin', this)" 
                    class="role-tab px-3 py-1 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 hover:bg-white/80 transition-colors">
                    Admin / Owner ({{ $admins->count() }})
                </button>
                <button 
                    type="button" 
                    onclick="selectRoleTab('staff', this)" 
                    class="role-tab px-3 py-1 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 hover:bg-white/80 transition-colors">
                    Kasir & Staff ({{ $staffs->count() }})
                </button>
                <button 
                    type="button" 
                    onclick="selectRoleTab('customer', this)" 
                    class="role-tab px-3 py-1 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 hover:bg-white/80 transition-colors">
                    Pelanggan ({{ $users->count() }})
                </button>
            </div>

            {{-- Status Dropdown --}}
            <div class="flex items-center space-x-2">
                <span class="text-neutral-500 text-[11px] font-mono">Status:</span>
                <select id="statusFilterSelect" onchange="applyStaffFilters()" class="bg-white border border-neutral-200 rounded-md px-2.5 py-1 text-xs text-neutral-700 focus:outline-none focus:ring-1 focus:ring-stone-800">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- 3. Unified Data Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="staffUnifiedTable">
                <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-500 text-[10px] font-mono uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Pengguna & Akun</th>
                        <th class="px-5 py-3 font-semibold">Role / Akses</th>
                        <th class="px-5 py-3 font-semibold text-center">Status</th>
                        <th class="px-5 py-3 font-semibold">Bergabung</th>
                        @if($isAdmin)
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 font-sans">
                    @forelse($allUsers as $person)
                        @php
                            $roleName = strtolower($person->role?->role_name ?? 'customer');
                            $roleGroup = 'customer';
                            if (in_array($roleName, ['admin', 'owner'])) {
                                $roleGroup = 'admin';
                            } elseif (in_array($roleName, ['staff', 'cashier', 'kasir', 'pegawai'])) {
                                $roleGroup = 'staff';
                            }

                            $status = strtolower($person->status ?? 'active');
                            $isSelf = $person->id_user === auth()->id();
                        @endphp
                        <tr class="staff-row hover:bg-neutral-50/75 transition-colors" data-role="{{ $roleGroup }}" data-status="{{ $status }}">
                            
                            {{-- Combined User Avatar + Full Name + Username / Email --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-md bg-neutral-100 border border-neutral-200 text-neutral-700 flex items-center justify-center font-mono font-semibold text-xs shrink-0">
                                        {{ strtoupper(substr($person->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-semibold text-neutral-900 truncate">{{ $person->name }}</span>
                                            @if($isSelf)
                                                <span class="text-[9.5px] font-mono bg-neutral-100 text-neutral-600 px-1.5 py-0.2 rounded border border-neutral-200">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-neutral-500 font-mono truncate mt-0.5">
                                            <span>&#64;{{ $person->username }}</span>
                                            @if(!empty($person->email))
                                                <span class="text-neutral-300 mx-1">•</span>
                                                <span>{{ $person->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role Column with Subtle Dot Indicator --}}
                            <td class="px-5 py-3">
                                @if($roleGroup === 'admin')
                                    <span class="inline-flex items-center space-x-1.5 text-[11px] font-mono px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-800 border border-neutral-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        <span class="font-medium">{{ $person->role?->role_name ?? 'Admin' }}</span>
                                    </span>
                                @elseif($roleGroup === 'staff')
                                    <span class="inline-flex items-center space-x-1.5 text-[11px] font-mono px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-700 border border-neutral-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                                        <span class="font-medium">{{ $person->role?->role_name ?? 'Staff' }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 text-[11px] font-mono px-2 py-0.5 rounded-md bg-neutral-50 text-neutral-600 border border-neutral-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                        <span>Customer</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Status Column --}}
                            <td class="px-5 py-3 text-center">
                                @if($status === 'active')
                                    <span class="inline-flex items-center space-x-1.5 text-[11px] font-mono px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 text-[11px] font-mono px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-600 border border-neutral-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Created Date --}}
                            <td class="px-5 py-3 text-[11px] font-mono text-neutral-500">
                                {{ $person->created_at ? $person->created_at->format('d/m/Y') : '-' }}
                            </td>

                            {{-- Actions Column --}}
                            @if($isAdmin)
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center space-x-1.5 relative">
                                        {{-- Primary Edit Button --}}
                                        <a href="{{ route('admin.staffoption.edit', $person->id_user ?? $person->id) }}" class="text-[11px] font-mono text-neutral-700 hover:text-neutral-900 border border-neutral-200 bg-white hover:bg-neutral-50 px-2.5 py-1 rounded transition-colors" title="Edit Akun">
                                            Edit
                                        </a>

                                        {{-- 3-Dot Dropdown Trigger --}}
                                        <div class="relative inline-block text-left">
                                            <button 
                                                type="button" 
                                                onclick="toggleActionDropdown('dropdown-{{ $person->id_user ?? $person->id }}', event)" 
                                                class="w-7 h-6 rounded border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-500 hover:text-neutral-800 flex items-center justify-center transition-colors">
                                                <i class="fas fa-ellipsis text-xs"></i>
                                            </button>

                                            {{-- Dropdown Menu --}}
                                            <div id="dropdown-{{ $person->id_user ?? $person->id }}" class="action-dropdown hidden absolute right-0 mt-1 w-44 bg-white border border-neutral-200 rounded-md shadow-md py-1 z-30 font-sans text-xs">
                                                
                                                {{-- Reset Password --}}
                                                <button 
                                                    type="button" 
                                                    onclick="openResetPasswordModal({{ $person->id_user ?? $person->id }}, '{{ addslashes($person->name) }}')" 
                                                    class="w-full text-left px-3 py-1.5 text-neutral-700 hover:bg-neutral-50 flex items-center space-x-2">
                                                    <i class="fas fa-key text-[10px] text-neutral-400 w-3.5"></i>
                                                    <span>Reset Password</span>
                                                </button>

                                                {{-- Toggle Active/Inactive --}}
                                                @if(!$isSelf)
                                                    <form method="POST" action="{{ route('admin.staff.toggle-status', $person->id_user ?? $person->id) }}" class="m-0">
                                                        @csrf
                                                        <button type="submit" class="w-full text-left px-3 py-1.5 text-neutral-700 hover:bg-neutral-50 flex items-center space-x-2">
                                                            <i class="fas {{ $status === 'active' ? 'fa-ban text-amber-500' : 'fa-check text-emerald-500' }} text-[10px] w-3.5"></i>
                                                            <span>{{ $status === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <div class="border-t border-neutral-100 my-1"></div>

                                                {{-- Delete Action --}}
                                                @if(!$isSelf)
                                                    <form method="POST" action="{{ route('admin.staff.destroy', $person->id_user ?? $person->id) }}" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ addslashes($person->name) }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 flex items-center space-x-2">
                                                            <i class="fas fa-trash-can text-[10px] w-3.5"></i>
                                                            <span>Hapus Akun</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 5 : 4 }}" class="px-5 py-12 text-center text-xs font-mono text-neutral-400">
                                Belum ada data pengguna yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 4. Clean Footer & Pagination Controls --}}
        <div class="px-5 py-3 border-t border-neutral-200 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-mono text-neutral-500">
            <span id="tablePaginationInfo">Menampilkan 1-{{ $allUsers->count() }} dari {{ $allUsers->count() }} pengguna</span>
            
            <div class="flex items-center space-x-1.5">
                <button type="button" class="px-2.5 py-1 rounded border border-neutral-200 bg-white text-neutral-400 cursor-not-allowed" disabled>
                    &larr; Prev
                </button>
                <button type="button" class="px-2.5 py-1 rounded border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
                    Next &rarr;
                </button>
            </div>
        </div>

    </div>

</div>

{{-- MODAL RESET PASSWORD --}}
<div id="resetPasswordModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-2xs flex items-center justify-center p-4">
    <div class="bg-white border border-neutral-200 rounded-xl shadow-lg w-full max-w-sm overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-5 py-4 border-b border-neutral-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-key text-xs text-neutral-600"></i>
                <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-neutral-900">Reset Password</h3>
            </div>
            <button type="button" onclick="closeResetPasswordModal()" class="text-neutral-400 hover:text-neutral-700 text-sm">&times;</button>
        </div>
        
        <form id="resetPasswordForm" method="POST" action="">
            @csrf
            <div class="p-5 space-y-4 text-xs">
                <p class="text-neutral-600 text-xs">
                    Reset password untuk akun: <strong class="text-neutral-900 font-semibold" id="resetTargetName">-</strong>
                </p>
                
                <div class="space-y-1.5">
                    <label class="font-medium text-neutral-700">Password Baru</label>
                    <input type="password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                </div>

                <div class="space-y-1.5">
                    <label class="font-medium text-neutral-700">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" required minlength="6" placeholder="Ulangi password baru" class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                </div>
            </div>

            <div class="px-5 py-3 border-t border-neutral-200 bg-neutral-50 flex justify-end space-x-2 text-xs font-mono">
                <button type="button" onclick="closeResetPasswordModal()" class="px-3 py-1.5 rounded border border-neutral-200 bg-white text-neutral-600 hover:bg-neutral-50">
                    Batal
                </button>
                <button type="submit" class="px-3.5 py-1.5 rounded bg-stone-900 text-white hover:bg-black font-medium">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentRoleFilter = 'all';

function selectRoleTab(role, buttonElement) {
    currentRoleFilter = role;

    // Update active tab styles
    document.querySelectorAll('#roleTabGroup .role-tab').forEach(tab => {
        tab.className = 'role-tab px-3 py-1 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 hover:bg-white/80 transition-colors';
    });
    buttonElement.className = 'role-tab px-3 py-1 rounded-md font-medium text-xs bg-white text-neutral-900 border border-neutral-200 shadow-2xs transition-colors';

    applyStaffFilters();
}

function applyStaffFilters() {
    const searchVal = document.getElementById('staffSearchInput').value.toLowerCase();
    const statusVal = document.getElementById('statusFilterSelect').value.toLowerCase();
    const rows = document.querySelectorAll('#staffUnifiedTable tbody .staff-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const role = row.getAttribute('data-role') || '';
        const status = row.getAttribute('data-status') || '';

        const matchSearch = text.includes(searchVal);
        const matchRole = (currentRoleFilter === 'all') || (role === currentRoleFilter);
        const matchStatus = !statusVal || (status === statusVal);

        if (matchSearch && matchRole && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const info = document.getElementById('tablePaginationInfo');
    if (info) {
        info.innerText = `Menampilkan ${visibleCount} dari ${rows.length} pengguna`;
    }
}

// 3-Dot Dropdown Menu Handler
function toggleActionDropdown(id, event) {
    event.stopPropagation();
    // Close other dropdowns
    document.querySelectorAll('.action-dropdown').forEach(dropdown => {
        if (dropdown.id !== id) dropdown.classList.add('hidden');
    });

    const target = document.getElementById(id);
    if (target) {
        target.classList.toggle('hidden');
    }
}

// Close dropdowns on outside click
window.addEventListener('click', function() {
    document.querySelectorAll('.action-dropdown').forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
});

// Modal Handlers
function openResetPasswordModal(idUser, name) {
    const modal = document.getElementById('resetPasswordModal');
    const form = document.getElementById('resetPasswordForm');
    document.getElementById('resetTargetName').innerText = name;
    form.action = `/admin/staff/${idUser}/reset-password`;
    modal.classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

document.getElementById('resetPasswordModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeResetPasswordModal();
});
</script>
@endsection