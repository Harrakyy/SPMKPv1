@extends('layouts.app')

@section('title', 'Master Schedule')

@push('styles')
<style>
    .alert-custom-blue {
        background-color: #f0f7ff;
        border: 1px solid #cce5ff;
        color: #004085;
        border-radius: 8px;
        padding: 15px;
        font-size: 0.85rem;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .alert-custom-blue i { color: #0d6efd; font-size: 1.1rem; }
    .alert-custom-yellow {
        background-color: #fffdf0;
        border: 1px solid #ffeeba;
        color: #856404;
        border-radius: 8px;
        padding: 15px;
        font-size: 0.85rem;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-top: 20px;
    }
    .alert-custom-yellow i { color: #fd7e14; font-size: 1.1rem; }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 20px;
        flex: 1;
    }
    .stat-icon {
        width: 35px; height: 35px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; margin-bottom: 15px;
    }
    .stat-icon-total { background-color: #eef2ff; color: #4f46e5; }
    .stat-icon-plan  { background-color: #e0f2fe; color: #0284c7; }
    .stat-icon-act   { background-color: #fff7ed; color: #ea580c; }
    .stat-icon-done  { background-color: #f0fdf4; color: #16a34a; }
    .stat-number { font-size: 1.8rem; font-weight: 700; margin-bottom: 0; line-height: 1; color: #333; }
    .stat-label  { font-size: 0.75rem; color: #666; margin-top: 5px; }
    .card-table-wrapper {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 20px;
        margin-top: 20px;
    }
    .table-custom { width: 100%; margin-bottom: 0; }
    .table-custom thead th {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #888;
        border-bottom: 1px solid #ededed;
        padding-bottom: 15px;
        font-weight: 600;
    }
    .table-custom tbody td {
        font-size: 0.85rem;
        padding: 15px 0;
        border-bottom: 1px solid #ededed;
        vertical-align: middle;
        color: #444;
    }
    .badge-code {
        background-color: #e0f2fe;
        color: #0284c7;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
        min-width: 40px;
        text-align: center;
    }
    .text-act  { color: #ea580c; font-weight: 600; font-size: 0.75rem; }
    .text-plan { color: #0d6efd; font-weight: 600; font-size: 0.75rem; }
    .form-label-custom { font-size: 0.8rem; color: #555; font-weight: 500; margin-bottom: 5px; }
    .form-control-custom, .form-select-custom {
        font-size: 0.85rem; padding: 10px 15px;
        border-radius: 8px; border: 1px solid #ddd;
    }
    #create-activity-container { display: none; }
    .edit-icon   { color: #0d6efd; }
    .delete-icon { color: #dc3545; }
</style>
@endpush

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════
     MAIN SCHEDULE VIEW
     ══════════════════════════════════════════════════════ --}}
<div id="master-schedule-main">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Master Schedule</h3>
            <p class="text-muted" style="font-size:0.85rem;">
                Monitoring & Activity Management — Data realtime dari Production Planning
            </p>
        </div>
        <button class="btn btn-primary btn-sm px-3 py-2" onclick="showCreateActivity()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Activity
        </button>
    </div>

    <div class="alert-custom-blue mb-4">
        <i class="bi bi-info-circle"></i>
        <div>
            <div class="fw-bold mb-1" style="font-size:0.8rem;">Activity Monitoring</div>
            <div style="font-size:0.8rem;">
                Jadwal ini memantau kemajuan produksi dari semua mesin.
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="d-flex gap-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon stat-icon-total"><i class="bi bi-calendar3"></i></div>
            <h2 class="stat-number">{{ $stats['total'] }}</h2>
            <div class="stat-label">Total Activities</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-plan"><i class="bi bi-clock"></i></div>
            <h2 class="stat-number text-primary">{{ $stats['plan'] }}</h2>
            <div class="stat-label text-primary">Plan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-act"><i class="bi bi-arrow-repeat"></i></div>
            <h2 class="stat-number" style="color:#ea580c;">{{ $stats['act'] }}</h2>
            <div class="stat-label" style="color:#ea580c;">Act</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-done"><i class="bi bi-check-circle"></i></div>
            <h2 class="stat-number text-success">{{ $stats['done'] }}</h2>
            <div class="stat-label text-success">Done</div>
        </div>
    </div>

    <div class="card-table-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="fw-bold mb-0">Master Schedule — Activity Monitoring</h6>
                <span class="text-muted" style="font-size:0.75rem;">
                    Data realtime dari Production Planning
                </span>
            </div>
            <select class="form-select form-select-sm" style="width:150px;"
                    onchange="filterByStatus(this.value)">
                <option value="">All Status</option>
                <option value="pending">Plan</option>
                <option value="running">Act</option>
                <option value="completed">Done</option>
            </select>
        </div>

        <table class="table table-custom">
            <thead>
                <tr>
                    <th>MACHINE</th>
                    <th>PART NAME</th>
                    <th>PIC</th>
                    <th>QTY</th>
                    <th>PLAN DATE</th>
                    <th>ACT DATE</th>
                    <th>PROCESS</th>
                    <th>ACTIONS</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody id="master-schedule-tbody">
                @forelse($activities as $act)
                <tr data-status="{{ $act->status }}">
                    <td>{{ $act->mesin->nama_mesin ?? '-' }}</td>
                    <td>{{ $act->partList->nama_part ?? '-' }}</td>
                    <td>{{ $act->pic ?? '-' }}</td>
                    <td>{{ $act->partList->quantity ?? '-' }}</td>
                    <td>{{ $act->tanggal_plan ? \Carbon\Carbon::parse($act->tanggal_plan)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $act->tanggal_actual ? \Carbon\Carbon::parse($act->tanggal_actual)->format('d/m/Y') : '-' }}</td>
                    <td>
                        <span class="badge-code">
                            {{ strtoupper(substr($act->mesin->jenis_proses ?? '-', 0, 4)) }}
                        </span>
                    </td>

                    {{-- ACTIONS --}}
                    <td>
                        <i class="bi bi-pencil edit-icon"
                           style="cursor:pointer; font-size:1rem; margin-right:10px;"
                           onclick="openEditModal(
                               {{ $act->mfg_id }},
                               '{{ addslashes($act->proses_nama) }}',
                               '{{ addslashes($act->pic ?? '') }}',
                               '{{ $act->tanggal_plan }}',
                               '{{ $act->tanggal_actual }}'
                           )"></i>
                        <i class="bi bi-trash delete-icon"
                           style="cursor:pointer; font-size:1rem;"
                           onclick="confirmDelete({{ $act->mfg_id }})"></i>
                    </td>

                    {{-- STATUS DROPDOWN --}}
                    <td>
                        <form action="/admin/master/{{ $act->mfg_id }}/status" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status"
                                    class="form-select form-select-sm"
                                    style="width:95px; font-size:0.75rem;"
                                    onchange="this.form.submit()">
                                <option value="pending"   {{ $act->status === 'pending'   ? 'selected' : '' }}>Plan</option>
                                <option value="running"   {{ $act->status === 'running'   ? 'selected' : '' }}>Act</option>
                                <option value="completed" {{ $act->status === 'completed' ? 'selected' : '' }}>Done</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada activity. Tambahkan melalui Production Planning.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="alert-custom-yellow">
        <i class="bi bi-exclamation-circle"></i>
        <div>
            <div class="fw-bold mb-1" style="font-size:0.8rem;">Admin Permissions</div>
            <div style="font-size:0.8rem;">
                Admin dapat melihat, menambah, mengedit, dan menghapus activity dari sini.
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     CREATE ACTIVITY VIEW
     ══════════════════════════════════════════════════════ --}}
<div id="create-activity-container">
    <button class="btn btn-link text-muted p-0 text-decoration-none mb-3"
            onclick="showMainSchedule()">
        <i class="bi bi-arrow-left me-1"></i> Back to Master Schedule
    </button>

    <h3 class="fw-bold mb-1">Create Activity</h3>
    <p class="text-muted" style="font-size:0.85rem;">Admin — Tambah activity baru ke jadwal produksi</p>

    <div class="card-table-wrapper">
        <h6 class="fw-bold mb-1">Activity Information</h6>
        <p class="text-muted border-bottom pb-3 mb-4" style="font-size:0.8rem;">
            Isi detail activity di bawah ini
        </p>

        <form action="{{ route('admin.master.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-custom">Mesin <span class="text-danger">*</span></label>
                    <select name="mesin_id" id="create-mesin-id"
                            class="form-select form-select-custom"
                            onchange="loadPartsForMesin(this.value)" required>
                        <option value="">-- Pilih Mesin --</option>
                        @foreach(\App\Models\Mesin::orderBy('nama_mesin')->get() as $m)
                            <option value="{{ $m->mesin_id }}"
                                    data-permintaan="{{ $m->permintaan_id }}">
                                {{ $m->kode_mesin }} — {{ $m->nama_mesin }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Part yang Dikerjakan</label>
                    <select name="partlist_id" id="create-partlist-id"
                            class="form-select form-select-custom">
                        <option value="">-- Pilih Mesin dulu --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Nama Activity <span class="text-danger">*</span></label>
                    <input type="text" name="nama_activity"
                           class="form-control form-control-custom"
                           placeholder="Contoh: CNC Milling Gear Shaft" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">PIC (Person in Charge) <span class="text-danger">*</span></label>
                    <input type="text" name="pic"
                           class="form-control form-control-custom"
                           placeholder="Nama operator" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Tanggal Plan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_plan"
                           class="form-control form-control-custom"
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Status Activity</label>
                    <select name="status" class="form-select form-select-custom">
                        <option value="pending">Plan</option>
                        <option value="running">Act</option>
                        <option value="completed">Done</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4"
                        onclick="showMainSchedule()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i> Create Activity
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL EDIT
     ══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; border:none;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Activity <span class="text-danger">*</span></label>
                        <input type="text" name="nama_activity" id="edit-nama"
                               class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIC <span class="text-danger">*</span></label>
                        <input type="text" name="pic" id="edit-pic"
                               class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Plan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_plan" id="edit-date"
                               class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Tanggal Actual</label>
                        <input type="date" name="tanggal_actual" id="edit-actual-date"
                               class="form-control">
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="button" class="btn btn-light border px-4"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL DELETE
     ══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:12px; border:none;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash text-danger fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold">Hapus Activity?</h5>
                <p class="text-muted mb-4" style="font-size:0.85rem;">
                    Data yang dihapus tidak dapat dikembalikan.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4"
                            data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
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
    const mainView   = document.getElementById('master-schedule-main');
    const createView = document.getElementById('create-activity-container');

    function showMainSchedule() {
        mainView.style.display   = 'block';
        createView.style.display = 'none';
        window.scrollTo(0, 0);
    }

    function showCreateActivity() {
        mainView.style.display   = 'none';
        createView.style.display = 'block';
        window.scrollTo(0, 0);
    }

    // Filter tabel berdasarkan status
    function filterByStatus(status) {
        document.querySelectorAll('#master-schedule-tbody tr[data-status]').forEach(row => {
            if (!status || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Buka modal edit
    function openEditModal(id, nama, pic, date, actualDate) {
        document.getElementById('edit-nama').value        = nama;
        document.getElementById('edit-pic').value         = pic;
        document.getElementById('edit-date').value        = date;
        document.getElementById('edit-actual-date').value = actualDate || '';
        document.getElementById('editForm').action        = '/admin/master/' + id;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    // Buka modal delete
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '/admin/master/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Load part list sesuai mesin yang dipilih (via AJAX)
    function loadPartsForMesin(mesinId) {
        const select = document.getElementById('create-partlist-id');
        select.innerHTML = '<option value="">Memuat...</option>';

        if (!mesinId) {
            select.innerHTML = '<option value="">-- Pilih Mesin dulu --</option>';
            return;
        }

        const selectedOption = document.querySelector(`#create-mesin-id option[value="${mesinId}"]`);
        const permintaanId   = selectedOption ? selectedOption.dataset.permintaan : null;

        if (!permintaanId) {
            select.innerHTML = '<option value="">-- Mesin tidak terkait permintaan --</option>';
            return;
        }

        fetch(`/admin/part-list?permintaan_id=${permintaanId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(parts => {
            if (!parts.length) {
                select.innerHTML = '<option value="">-- Belum ada part --</option>';
                return;
            }
            select.innerHTML = '<option value="">-- Pilih Part (opsional) --</option>';
            parts.forEach(p => {
                const opt       = document.createElement('option');
                opt.value       = p.partlist_id;
                opt.textContent = p.nama_part + (p.material ? ` — ${p.material}` : '');
                select.appendChild(opt);
            });
        })
        .catch(() => {
            select.innerHTML = '<option value="">Gagal memuat part</option>';
        });
    }
</script>
@endpush