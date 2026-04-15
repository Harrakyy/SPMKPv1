@extends('layouts.app')

@section('title', 'User Management')

@section('styles')
<style>
    .page-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e0e0e0;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .table-custom thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #888;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #ededed;
        padding: 12px 16px;
        font-weight: 600;
        background: #fafafa;
    }
    .table-custom tbody td {
        font-size: 0.88rem;
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f5f5f5;
    }
    .table-custom tbody tr:last-child td { border-bottom: none; }
    .table-custom tbody tr:hover { background: #fafcff; }
    .avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .role-badge {
        font-size: 0.72rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .role-admin      { background: #fde8e8; color: #c0392b; }
    .role-engineer   { background: #e8f0fe; color: #1a56db; }
    .role-operator   { background: #e8f8ee; color: #1a7a3f; }
    .role-supervisor { background: #fef3e8; color: #b45309; }
    .role-default    { background: #f0f0f0; color: #555; }
    .action-icon { cursor: pointer; transition: transform 0.2s; font-size: 1rem; }
    .action-icon:hover { transform: scale(1.15); }
    .icon-edit   { color: #0d6efd; }
    .icon-delete { color: #dc3545; }
    .search-input {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 8px 14px;
        font-size: 0.87rem;
        width: 240px;
    }
    .search-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
</style>
@endsection

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1">User Management</h3>
        <p class="text-muted mb-0">Kelola akun pengguna dan hak akses sistem</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus me-1"></i> Tambah User
    </button>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        ✅ {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        ⚠️ {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
        $totalUsers    = $users->count();
        $totalAdmin    = $users->where('role', 'admin')->count();
        $totalEngineer = $users->where('role', 'engineer')->count();
        $totalOperator = $users->where('role', 'operator')->count();
    @endphp
    <div class="col-6 col-md-3">
        <div class="page-card p-3 text-center mb-0">
            <div class="fw-bold fs-4 text-primary">{{ $totalUsers }}</div>
            <div class="text-muted" style="font-size:0.8rem;">Total User</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="page-card p-3 text-center mb-0">
            <div class="fw-bold fs-4" style="color:#c0392b;">{{ $totalAdmin }}</div>
            <div class="text-muted" style="font-size:0.8rem;">Admin</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="page-card p-3 text-center mb-0">
            <div class="fw-bold fs-4" style="color:#1a56db;">{{ $totalEngineer }}</div>
            <div class="text-muted" style="font-size:0.8rem;">Engineer</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="page-card p-3 text-center mb-0">
            <div class="fw-bold fs-4" style="color:#1a7a3f;">{{ $totalOperator }}</div>
            <div class="text-muted" style="font-size:0.8rem;">Operator</div>
        </div>
    </div>
</div>

{{-- Tabel User --}}
<div class="page-card">
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
        <h6 class="fw-bold mb-0" style="font-size:0.9rem;">Daftar Pengguna</h6>
        <input type="text" class="search-input" placeholder="🔍 Cari nama atau email..."
               onkeyup="filterTable(this.value)">
    </div>
    <table class="table table-custom mb-0" id="userTable">
        <thead>
            <tr>
                <th>NO</th>
                <th>PENGGUNA</th>
                <th>EMAIL</th>
                <th>ROLE</th>
                <th>JABATAN</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            @forelse($users as $user)
            <tr>
                <td class="text-muted fw-bold">
                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-sm">
                            {{ strtoupper(substr($user->nama ?? $user->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="fw-semibold">{{ $user->nama ?? $user->name ?? '-' }}</span>
                    </div>
                </td>
                <td class="text-muted">{{ $user->email }}</td>
                <td>
                    @php
                        $roleClass = match($user->role ?? '') {
                            'admin'      => 'role-admin',
                            'engineer'   => 'role-engineer',
                            'operator'   => 'role-operator',
                            'supervisor' => 'role-supervisor',
                            default      => 'role-default',
                        };
                    @endphp
                    <span class="role-badge {{ $roleClass }}">
                        {{ $user->role ?? '-' }}
                    </span>
                </td>
                <td class="text-muted">{{ $user->jabatan ?? '-' }}</td>
                <td>
                    {{-- Jangan tampilkan tombol hapus untuk diri sendiri --}}
                    @if($user->user_id !== auth()->id())
                        <i class="bi bi-pencil action-icon icon-edit me-2" title="Edit"
                           onclick="openEditModal(
                               {{ $user->user_id }},
                               '{{ addslashes($user->nama ?? $user->name) }}',
                               '{{ $user->email }}',
                               '{{ $user->role ?? '' }}',
                               '{{ addslashes($user->jabatan ?? '') }}'
                           )"></i>
                        <i class="bi bi-trash action-icon icon-delete" title="Hapus"
                           onclick="confirmDelete({{ $user->user_id }}, '{{ addslashes($user->nama ?? $user->name) }}')"></i>
                    @else
                        <span class="text-muted" style="font-size:0.78rem;">— (Anda)</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                    Belum ada pengguna terdaftar.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===== MODAL TAMBAH USER ===== --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold">Tambah User Baru</h5>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">Daftarkan pengguna baru ke sistem</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4 pt-2">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control"
                                   placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="email@domain.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Minimal 8 karakter" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select name="role" class="form-select" required>
                                <option value="" disabled selected>Pilih role</option>
                                <option value="admin">Admin</option>
                                <option value="engineer">Engineer</option>
                                <option value="operator">Operator</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Jabatan
                            </label>
                            <input type="text" name="jabatan" class="form-control"
                                   placeholder="Contoh: CNC Operator">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                No. HP
                            </label>
                            <input type="text" name="no_hp" class="form-control"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="button" class="btn btn-light border px-4"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-person-check me-1"></i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ===== MODAL EDIT USER ===== --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold">Edit User</h5>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">Update informasi pengguna</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4 pt-2">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="edit-email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Password Baru
                            </label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Kosongkan jika tidak diubah">
                            <div class="form-text" style="font-size:0.75rem;">
                                Kosongkan jika tidak ingin mengganti password.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select name="role" id="edit-role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="engineer">Engineer</option>
                                <option value="operator">Operator</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size:0.85rem;">Jabatan</label>
                            <input type="text" name="jabatan" id="edit-jabatan" class="form-control"
                                   placeholder="Jabatan">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="button" class="btn btn-light border px-4"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ===== MODAL HAPUS USER ===== --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:12px;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold">Hapus User?</h5>
                <p class="text-muted mb-1" style="font-size:0.9rem;">
                    Akun <strong id="delete-user-name"></strong> akan dihapus permanen.
                </p>
                <p class="text-muted mb-4" style="font-size:0.82rem;">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4"
                            data-bs-dismiss="modal">Batal</button>
                    <form id="deleteUserForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openEditModal(id, nama, email, role, jabatan) {
        document.getElementById('edit-nama').value    = nama;
        document.getElementById('edit-email').value   = email;
        document.getElementById('edit-role').value    = role;
        document.getElementById('edit-jabatan').value = jabatan;
        document.getElementById('editUserForm').action = '/admin/users/' + id;
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    function confirmDelete(id, nama) {
        document.getElementById('delete-user-name').textContent = nama;
        document.getElementById('deleteUserForm').action = '/admin/users/' + id;
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }

    function filterTable(query) {
        const rows = document.querySelectorAll('#userTableBody tr');
        const q = query.toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
@endpush