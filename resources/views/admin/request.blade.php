@extends('layouts.app')

@push('styles')
<style>
    .card-custom { background-color: #fff; border-radius: 12px; border: 1px solid #e0e0e0; padding: 25px; margin-bottom: 25px; }
    .table-custom { width: 100%; margin-top: 15px; }
    .table-custom thead th { font-size: 0.75rem; text-transform: uppercase; color: #888; letter-spacing: 0.5px; padding-bottom: 15px; border-bottom: 1px solid #ededed; }
    .table-custom tbody td { font-size: 0.9rem; padding: 18px 0; border-bottom: 1px solid #ededed; vertical-align: middle; }
    .machine-name { font-weight: 600; color: #333; }
    .machine-desc { font-size: 0.8rem; color: #777; margin-top: 3px; }

    /* Status Badges */
    .status-badge        { font-size: 0.8rem; padding: 4px 10px; border-radius: 20px; font-weight: 500; }
    .status-draft        { background-color: #e2e3e5; color: #41464b; }
    .status-submitted    { background-color: #cfe2ff; color: #084298; }
    .status-approved     { background-color: #d1e7dd; color: #0f5132; }
    .status-rejected     { background-color: #f8d7da; color: #842029; }
    .status-in_progress  { background-color: #fff3cd; color: #856404; }
    .status-completed    { background-color: #d1e7dd; color: #0f5132; }

    /* Part Status Badges */
    .status-belum_dibeli { background-color: #e2e3e5; color: #41464b; }
    .status-ready        { background-color: #d1e7dd; color: #0f5132; }
    .status-selesai      { background-color: #d1e7dd; color: #0f5132; }

    /* Priority Badges */
    .priority-low    { background: #e2e3e5; color: #41464b; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; }
    .priority-medium { background: #cfe2ff; color: #084298; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; }
    .priority-high   { background: #fff3cd; color: #856404; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; }
    .priority-urgent { background: #f8d7da; color: #842029; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; }

    /* Action Icons */
    .action-icons { display: flex; gap: 12px; font-size: 1.1rem; align-items: center; }
    .action-icon { cursor: pointer; transition: transform 0.2s; }
    .action-icon:hover { transform: scale(1.1); }
    .icon-view    { color: #198754; }
    .icon-edit    { color: #0d6efd; }
    .icon-delete  { color: #dc3545; }
    .icon-approve { color: #198754; }
    .icon-reject  { color: #dc3545; }

    /* Modals */
    .modal-content { border-radius: 12px; border: none; padding: 10px; }
    .modal-header  { border-bottom: none; padding-bottom: 5px; }
    .modal-footer  { border-top: none; padding-top: 5px; }
    .form-label-custom { font-size: 0.85rem; color: #666; margin-bottom: 5px; display: block; }
    .form-control-custom { border-radius: 8px; padding: 10px 15px; border: 1px solid #ccc; }
    .form-control-custom:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13,110,253,.15); }
    .required-star { color: #dc3545; margin-left: 3px; }

    /* Part Listing */
    #part-listing-container { display: none; }
    .part-detail-label { font-size: 0.8rem; color: #888; margin-bottom: 2px; }
    .part-detail-value { font-size: 1rem; color: #333; font-weight: 500; }

    /* Progress */
    .progress-sm { height: 6px; border-radius: 99px; }
</style>
@endpush

@section('content')

{{-- ── REQUEST LIST ── --}}
<div id="request-management-container">
    <div class="page-header d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2>Request Management</h2>
            <p class="text-muted">Input & manage incoming production requests</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#addRequestModal">
            <i class="bi bi-plus-lg"></i> Tambah Permintaan
        </button>
    </div>

    <div class="card-custom">
        {{-- Filter --}}
        <div class="d-flex justify-content-between gap-3 mb-4">
            <div class="input-group" style="max-width:500px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="tableSearch" class="form-control border-start-0"
                       placeholder="Cari Request ID, jenis produk...">
            </div>
            <select id="statusFilter" class="form-select" style="max-width:160px;">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <table class="table-custom" id="requestTable">
            <thead>
                <tr>
                    <th>REQUEST ID</th>
                    <th>JENIS PRODUK</th>
                    <th>PRIORITY</th>
                    <th>DUE DATE</th>
                    <th>PARTS</th>
                    <th>PROGRESS</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody id="request-table-body">
                @forelse($permintaan as $p)
                <tr data-status="{{ $p->status }}">
                    <td>{{ $p->nomor_permintaan }}</td>
                    <td>
                        <div class="machine-name">{{ $p->jenis_produk ?? '-' }}</div>
                        <div class="machine-desc">{{ Str::limit($p->deskripsi_kebutuhan, 60) }}</div>
                    </td>
                    <td>
                        <span class="priority-{{ $p->priority }}">{{ ucfirst($p->priority) }}</span>
                    </td>
                    <td>{{ $p->tanggal_selesai ? $p->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                    <td>{{ $p->partLists->count() }} parts</td>
                    <td style="min-width:100px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress progress-sm flex-grow-1">
                                <div class="progress-bar bg-success" style="width:{{ $p->progress }}%"></div>
                            </div>
                            <small class="text-muted">{{ $p->progress }}%</small>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $p->status }}">
                            {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-icons">
                            <i class="bi bi-eye action-icon icon-view"
                               title="Lihat Part List"
                               onclick="showPartListing('{{ $p->nomor_permintaan }}', '{{ addslashes($p->jenis_produk) }}', {{ $p->permintaan_id }})">
                            </i>

                            @if($p->status === 'submitted')
                            <form action="{{ route('admin.permintaan.approve', $p->permintaan_id) }}"
                                  method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn p-0 border-0 bg-transparent" title="Approve"
                                        onclick="return confirm('Setujui permintaan ini?')">
                                    <i class="bi bi-check-circle action-icon icon-approve"></i>
                                </button>
                            </form>
                            <button class="btn p-0 border-0 bg-transparent" title="Reject"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal"
                                    onclick="setRejectId({{ $p->permintaan_id }})">
                                <i class="bi bi-x-circle action-icon icon-reject"></i>
                            </button>
                            @endif

                            <form action="{{ route('admin.permintaan.destroy', $p->permintaan_id) }}"
                                  method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bg-transparent" title="Hapus"
                                        onclick="return confirm('Hapus permintaan {{ $p->nomor_permintaan }}?')">
                                    <i class="bi bi-trash action-icon icon-delete"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Belum ada permintaan masuk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($permintaan->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $permintaan->links() }}
        </div>
        @endif
    </div>
</div>


{{-- ── PART LISTING ── --}}
<div id="part-listing-container">
    <div class="page-header mb-4">
        <a href="#" class="text-decoration-none text-muted" onclick="hidePartListing()">
            <i class="bi bi-arrow-left me-2"></i> Back to Request Management
        </a>
        <div class="d-flex justify-content-between align-items-start mt-3">
            <div>
                <h2>Part Listing</h2>
                <p class="text-muted mb-0">Request ID: <span id="detail-req-id" class="fw-bold"></span></p>
                <p class="text-muted">Jenis Produk: <span id="detail-machine-name" class="fw-bold"></span></p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2" onclick="openAddPartModal()">
                <i class="bi bi-plus-lg"></i> Tambah Part
            </button>
        </div>
    </div>

    <div class="card-custom">
        <h5 class="mb-4 fw-bold">Daftar Part</h5>
        <div id="part-list-content">
            <p class="text-muted">Memuat data...</p>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════
     MODALS
     ════════════════════════════════════════════════ --}}

{{-- ── MODAL: TAMBAH PERMINTAAN ── --}}
<div class="modal fade" id="addRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title fw-bold">Create Machine Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.permintaan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_from_modal" value="addRequestModal">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Jenis Produk / Mesin<span class="required-star">*</span>
                            </label>
                            <input type="text" name="jenis_produk"
                                   class="form-control form-control-custom @error('jenis_produk') is-invalid @enderror"
                                   placeholder="Contoh: Mesin Conveyor Custom"
                                   value="{{ old('jenis_produk') }}" required>
                            @error('jenis_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Priority<span class="required-star">*</span>
                            </label>
                            <select name="priority"
                                    class="form-select form-control-custom @error('priority') is-invalid @enderror"
                                    required>
                                <option value="low"    {{ old('priority') == 'low'    ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority','medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high"   {{ old('priority') == 'high'   ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">
                            Deskripsi Kebutuhan<span class="required-star">*</span>
                        </label>
                        <textarea name="deskripsi_kebutuhan"
                                  class="form-control form-control-custom @error('deskripsi_kebutuhan') is-invalid @enderror"
                                  rows="4"
                                  placeholder="Detail kebutuhan dan spesifikasi mesin..."
                                  required>{{ old('deskripsi_kebutuhan') }}</textarea>
                        @error('deskripsi_kebutuhan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Tanggal Permintaan<span class="required-star">*</span>
                            </label>
                            <input type="date" name="tanggal_permintaan"
                                   class="form-control form-control-custom @error('tanggal_permintaan') is-invalid @enderror"
                                   value="{{ old('tanggal_permintaan', date('Y-m-d')) }}" required>
                            @error('tanggal_permintaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Due Date</label>
                            <input type="date" name="tanggal_selesai"
                                   class="form-control form-control-custom @error('tanggal_selesai') is-invalid @enderror"
                                   value="{{ old('tanggal_selesai') }}">
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control form-control-custom"
                                  rows="2" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ── MODAL: TAMBAH PART ── --}}
<div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title fw-bold">Tambah Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPartForm" method="POST" action="/admin/part-list">
                @csrf
                <input type="hidden" name="permintaan_id" id="add-part-permintaan-id">
                <div class="modal-body">

                    {{-- Nama Part & Material --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Nama Part<span class="required-star">*</span>
                            </label>
                            <input type="text" name="nama_part"
                                   class="form-control form-control-custom"
                                   placeholder="Contoh: Gear Shaft" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Material<span class="required-star">*</span>
                            </label>

                            <select id="material_select" class="form-control form-control-custom"
                                    onchange="handleMaterialChange(this)">
                                <option value="">-- Pilih Material --</option>
                                <option value="SS400">SS400</option>
                                <option value="S45C">S45C</option>
                                <option value="SKD11">SKD11</option>
                                <option value="SKD61">SKD61</option>
                                <option value="SCM415">SCM415</option>
                                <option value="SCM440">SCM440</option>
                                <option value="SUS304">SUS304</option>
                                <option value="ALUMUNIUM7075">ALUMUNIUM7075</option>
                                <option value="ALUMUNIUM7065">ALUMUNIUM7065</option>
                                <option value="SPCC/SPHC">SPCC/SPHC</option>
                                <option value="CARBIDE">CARBIDE</option>
                                <option value="FC200">FC200</option>
                                <option value="FC400">FC400</option>
                                <option value="BRONZE">BRONZE</option>
                                <option value="URETHANE">URETHANE</option>
                                <option value="NYLON">NYLON</option>
                                <option value="HOLLOW">HOLLOW</option>
                                <option value="SIKU">SIKU</option>
                                <option value="H BEAM">H BEAM</option>
                                <option value="UNP">UNP</option>
                                <option value="EXPAND">EXPAND</option>
                                <option value="BOARDESK">BOARDESK</option>
                                <option value="PLAT">PLAT</option>
                                <option value="lainnya">Lainnya (input manual)</option>
                            </select>

                            <input type="text" id="material_input"
                                   class="form-control form-control-custom mt-2"
                                   placeholder="Atau ketik material manual di sini..."
                                   oninput="handleMaterialInput(this)">

                            <input type="hidden" name="material" id="material_final">

                            <small class="text-muted" style="font-size:0.75rem;">
                                Pilih dari dropdown atau ketik manual di kolom atas
                            </small>
                        </div>
                    </div>

                    {{-- Dimension Finish & Dimension Raw --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Dimension Finish</label>
                            <input type="text" name="dimensi" id="dimensi_finish"
                                   class="form-control form-control-custom"
                                   placeholder="panjang x lebar x tinggi">
                            <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:4px;">
                                <small class="text-muted" style="width:100%; margin-bottom:2px;">Simbol:</small>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_finish', 'Ø')">Ø</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_finish', 'R')">R</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_finish', ' x ')">x</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_finish', '±')">±</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_finish', '°')">°</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Dimension Raw</label>
                            <input type="text" name="dimensi_belanja" id="dimensi_raw"
                                   class="form-control form-control-custom"
                                   placeholder="panjang x lebar x tinggi">
                            <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:4px;">
                                <small class="text-muted" style="width:100%; margin-bottom:2px;">Simbol:</small>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_raw', 'Ø')">Ø</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_raw', 'R')">R</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_raw', ' x ')">x</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_raw', '±')">±</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertSymbol('dimensi_raw', '°')">°</button>
                            </div>
                        </div>
                    </div>

                    {{-- Qty & Purchase By --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Qty<span class="required-star">*</span>
                            </label>
                            <input type="number" name="quantity"
                                   class="form-control form-control-custom"
                                   min="1" placeholder="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">
                                Purchase By<span class="required-star">*</span>
                            </label>
                            <select name="purchase" id="purchase_by_select"
                                    class="form-control form-control-custom" required>
                                <option value="">-- Pilih Pembeli --</option>
                                <option value="Material">Mechanic</option>
                                <option value="Electric">Electric</option>
                            </select>
                        </div>
                    </div>

                    {{-- Status Part --}}
                    <div class="mb-3">
                        <label class="form-label-custom">Status Part</label>
                        <select name="status_part" class="form-select form-control-custom">
                            <option value="belum_dibeli">Belum Dibeli</option>
                            <option value="ready">Ready</option>
                            <option value="dibeli">Dibeli</option>
                            <option value="indent">Indent</option>
                        </select>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label class="form-label-custom">Catatan</label>
                        <textarea name="catatan" class="form-control form-control-custom"
                                  rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-start gap-2">
                    {{-- Tombol submit pakai onclick AJAX, bukan submit biasa --}}
                    <button type="button" class="btn btn-primary px-4" onclick="submitAddPart()">Simpan</button>
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL: EDIT STATUS PART ── --}}
<div class="modal fade" id="editPartStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title fw-bold">Edit Status Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPartStatusForm" method="POST">
                @csrf
                    {{-- @method('PATCH') --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom text-muted">Nama Part</label>
                        <div id="edit-part-name-display" class="fw-bold fs-5 text-dark">-</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Status Part</label>
                        <select name="status_part" id="edit-part-status-select"
                                class="form-select form-control-custom">
                            <option value="belum_dibeli">Belum Dibeli</option>
                            <option value="ready">Ready</option>
                            <option value="in_progress">In Progress</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <p class="text-muted mt-2" style="font-size:0.8rem;">
                        Hanya part dengan status "Ready" yang dapat dijadwalkan untuk produksi.
                    </p>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div> 
            </form>
        </div>
    </div>
</div>


{{-- ── MODAL: DETAIL PART ── --}}
<div class="modal fade" id="partDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between pb-2">
                <h5 class="modal-title fw-bold">Detail Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="part-detail-label">Nama Part</div>
                            <div class="part-detail-value" id="detail-nama-part">-</div>
                        </div>
                        <div class="mb-3">
                            <div class="part-detail-label">Dimension Finish</div>
                            <div class="part-detail-value" id="detail-dimensi-finish">-</div>
                        </div>
                        <div class="mb-3">
                            <div class="part-detail-label">Quantity</div>
                            <div class="part-detail-value" id="detail-qty">-</div>
                        </div>
                        <div class="mb-3">
                            <div class="part-detail-label">Purchase By</div>
                            <div class="part-detail-value" id="purchase">-</div>
                        </div>
                        <div class="mb-3">
                            <div class="part-detail-label">Status</div>
                            <div id="detail-status-part">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="part-detail-label">Material</div>
                            <div class="part-detail-value" id="detail-material">-</div>
                        </div>
                        <div class="mb-3">
                            <div class="part-detail-label">Dimension Raw</div>
                            <div class="part-detail-value" id="detail-dimensi-raw">-</div>
                        </div>
                        <div class="mb-3">
                            <div class="part-detail-label">Catatan</div>
                            <div class="part-detail-value" id="detail-catatan">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


{{-- ── MODAL: REJECT ── --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger">Tolak Permintaan?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label-custom">Alasan penolakan (opsional)</label>
                    <textarea name="catatan" class="form-control form-control-custom"
                              rows="3" placeholder="Tulis alasan penolakan..."></textarea>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPermintaanId = null;
    let partsData = {}; // ── Simpan data parts global

    // ── NAVIGASI REQUEST <-> PART LISTING ──
    function showPartListing(reqId, machineName, permintaanId) {
        currentPermintaanId = permintaanId;
        document.getElementById('request-management-container').style.display = 'none';
        document.getElementById('part-listing-container').style.display       = 'block';
        document.getElementById('detail-req-id').innerText                    = reqId;
        document.getElementById('detail-machine-name').innerText              = machineName;
        loadPartList(permintaanId);
    }

    function hidePartListing() {
        document.getElementById('part-listing-container').style.display       = 'none';
        document.getElementById('request-management-container').style.display = 'block';
        currentPermintaanId = null;
    }

    function openAddPartModal() {
    document.getElementById('addPartForm').reset();
    document.getElementById('material_input').value  = '';
    document.getElementById('material_final').value  = '';
    document.getElementById('material_select').value = '';
    // ✅ Reset purchase_by juga
    document.getElementById('purchase_by_select').value = '';
    document.getElementById('add-part-permintaan-id').value = currentPermintaanId;
    new bootstrap.Modal(document.getElementById('addPartModal')).show();
}

function submitAddPart() {
    const form      = document.getElementById('addPartForm');
    const formData  = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ✅ Validasi material
    const materialFinal = document.getElementById('material_final').value.trim();
    if (!materialFinal) {
        alert('Silakan pilih atau isi material terlebih dahulu.');
        return;
    }

    // ✅ Validasi purchase_by (sering terlewat)
    const purchaseBy = document.getElementById('purchase_by_select').value;
    if (!purchaseBy) {
        alert('Silakan pilih Purchase By.');
        return;
    }
   
    // ✅ Pastikan material_final terisi di FormData
    formData.set('material', materialFinal);

    fetch('/admin/part-list', {
        method : 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN'    : csrfToken,
        },
        body: formData
    })
    .then(async r => {
        if (!r.ok) {
            const errBody = await r.json().catch(() => ({}));
            // ✅ Tampilkan error validasi spesifik dari backend
            const messages = errBody.errors
                ? Object.values(errBody.errors).flat().join('\n')
                : (errBody.message ?? `HTTP ${r.status}`);
            throw new Error(messages);
        }
        return r.json();
    })
    .then(res => {
        if (res.success || res.partlist_id) {
            bootstrap.Modal.getInstance(document.getElementById('addPartModal')).hide();
            loadPartList(currentPermintaanId);
        } else {
            alert('Gagal menyimpan part: ' + (res.message ?? 'Unknown error'));
        }
    })
    .catch(err => {
        console.error('Submit error:', err);
        alert('Error:\n' + err.message);
    });
}
    // ── LOAD PART LIST ──
    function loadPartList(permintaanId) {
        const container = document.getElementById('part-list-content');
        container.innerHTML = '<p class="text-muted"><i class="bi bi-hourglass-split me-2"></i>Memuat data...</p>';

        fetch(`/admin/part-list?permintaan_id=${permintaanId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(parts => {
            if (!parts.length) {
                container.innerHTML = `
                    <p class="text-muted text-center py-5">
                        <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                        Belum ada part list untuk permintaan ini.
                    </p>`;
                return;
            }

            // ── Simpan semua data part ke variable global ──
            partsData = {};
            parts.forEach(p => { partsData[p.partlist_id] = p; });

            const statusBadgeClass = {
                'belum_dibeli': 'status-draft',
                'ready'       : 'status-approved',
                'in_progress' : 'status-in_progress',
                'selesai'     : 'status-completed',
            };
            const statusLabel = {
                'belum_dibeli': 'Belum Dibeli',
                'ready'       : 'Ready',
                'in_progress' : 'In Progress',
                'selesai'     : 'Selesai',
            };

            const rows = parts.map(p => `
                <tr id="part-row-${p.partlist_id}">
                    <td>${p.nama_part ?? '-'}</td>
                    <td>${p.material ?? '-'}</td>
                    <td>${p.dimensi ?? '-'}</td>
                    <td>${p.dimensi_belanja ?? '-'}</td>
                    <td>${p.quantity ?? '-'}</td>
                    <td>${p.purchase ?? '-'}</td> 
                    <td>
                        <span class="status-badge ${statusBadgeClass[p.status_part] ?? 'status-draft'}">
                            ${statusLabel[p.status_part] ?? p.status_part}
                        </span>
                    </td>
                    <td>
                        <div class="action-icons">
                            <i class="bi bi-eye action-icon icon-view"
                               title="Detail Part"
                               onclick="showPartDetail(${p.partlist_id})"></i>
                            <i class="bi bi-pencil action-icon icon-edit"
                               title="Edit Status"
                               data-bs-toggle="modal"
                               data-bs-target="#editPartStatusModal"
                               onclick="openEditPartStatus(${p.partlist_id}, '${escapeHtml(p.nama_part)}', '${p.status_part}')"></i>
                            <button class="btn p-0 border-0 bg-transparent" title="Hapus Part"
                                    onclick="deletePart(${p.partlist_id}, '${escapeHtml(p.nama_part)}')">
                                <i class="bi bi-trash action-icon icon-delete"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            container.innerHTML = `
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>PART NAME</th>
                            <th>MATERIAL</th>
                            <th>DIMENSION FINISH</th>
                            <th>DIMENSION RAW</th>
                            <th>QTY</th>
                            <th>PURCHASE BY</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>`;
        })
        .catch(() => {
            container.innerHTML = '<p class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat data part list.</p>';
        });
    }

    // ── DETAIL PART ── (terima ID, ambil dari partsData)
    function showPartDetail(partId) {
        const p = partsData[partId];
        if (!p) {
            alert('Data part tidak ditemukan.');
            return;
        }

        const statusLabel = {
            'belum_dibeli': 'Belum Dibeli',
            'ready'       : 'Ready',
            'in_progress' : 'In Progress',
            'selesai'     : 'Selesai',
        };
        const statusClass = {
            'belum_dibeli': 'status-draft',
            'ready'       : 'status-approved',
            'in_progress' : 'status-in_progress',
            'selesai'     : 'status-completed',
        };

        document.getElementById('detail-nama-part').innerText     = p.nama_part       ?? '-';
        document.getElementById('detail-material').innerText       = p.material        ?? '-';
        document.getElementById('detail-dimensi-finish').innerText = p.dimensi         ?? '-';
        document.getElementById('detail-dimensi-raw').innerText    = p.dimensi_belanja ?? '-';
        document.getElementById('detail-qty').innerText            = p.quantity        ?? '-';
        document.getElementById('purchase').innerText = p.purchase ?? '-';
        document.getElementById('detail-catatan').innerText        = p.catatan         ?? '-';
        document.getElementById('detail-status-part').innerHTML    =
            `<span class="status-badge ${statusClass[p.status_part] ?? 'status-draft'}">
                ${statusLabel[p.status_part] ?? p.status_part}
             </span>`;

        new bootstrap.Modal(document.getElementById('partDetailModal')).show();
    }

    // ── EDIT STATUS PART ──
    function openEditPartStatus(partId, partName, currentStatus) {
        document.getElementById('edit-part-name-display').innerText = partName;
        document.getElementById('edit-part-status-select').value    = currentStatus;

        const form = document.getElementById('editPartStatusForm');
        form.action = `/admin/part-list/${partId}/status`;

        // Hapus _method jika ada
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
    }

    // ── HAPUS PART ──
    function deletePart(partId, partName) {
        if (!confirm(`Hapus part "${partName}" dari daftar?`)) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/admin/part-list/${partId}`, {
            method: 'POST',
            headers: {
                'Content-Type'    : 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN'    : csrfToken,
            },
            body: `_method=DELETE&_token=${csrfToken}`
        })
        .then(r => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
        })
        .then(res => {
            if (res.success) {
                const row = document.getElementById(`part-row-${partId}`);
                if (row) row.remove();
                // Hapus juga dari partsData
                delete partsData[partId];
            } else {
                alert('Gagal menghapus part: ' + (res.message ?? 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            alert('Terjadi kesalahan saat menghapus. Cek console untuk detail.');
        });
    }

    // ── REJECT MODAL ──
    function setRejectId(permintaanId) {
        document.getElementById('rejectForm').action = `/admin/permintaan/${permintaanId}/reject`;
    }

    // ── MATERIAL: HANDLE DROPDOWN + MANUAL ──
    function handleMaterialChange(select) {
        const inputManual = document.getElementById('material_input');
        const finalInput  = document.getElementById('material_final');

        if (select.value === 'lainnya') {
            inputManual.value = '';
            finalInput.value  = '';
            inputManual.focus();
        } else if (select.value !== '') {
            inputManual.value = select.value;
            finalInput.value  = select.value;
        } else {
            inputManual.value = '';
            finalInput.value  = '';
        }
    }

    function handleMaterialInput(input) {
        const finalInput = document.getElementById('material_final');
        finalInput.value = input.value;

        // Reset dropdown ke placeholder jika user ketik sendiri
        const select = document.getElementById('material_select');
        if (input.value === '') {
            select.value = '';
        }
    }

    // ── INSERT SIMBOL DIMENSI ──
    function insertSymbol(inputId, symbol) {
        const input = document.getElementById(inputId);
        const start = input.selectionStart;
        const end   = input.selectionEnd;
        input.value = input.value.substring(0, start) + symbol + input.value.substring(end);
        input.selectionStart = input.selectionEnd = start + symbol.length;
        input.focus();
    }

    // ── FILTER TABEL ──
    document.getElementById('tableSearch').addEventListener('keyup', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);

    function filterTable() {
        const search = document.getElementById('tableSearch').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;

        document.querySelectorAll('#request-table-body tr').forEach(row => {
            const matchSearch = row.innerText.toLowerCase().includes(search);
            const matchStatus = !status || (row.dataset.status ?? '') === status;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    // ── HELPER ──
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // ── RE-BUKA MODAL JIKA ADA VALIDATION ERROR ──
    @if($errors->any() && old('_from_modal') === 'addRequestModal')
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('addRequestModal')).show();
    });
    @endif

    @isset($open_permintaan)
document.addEventListener('DOMContentLoaded', function () {
    showPartListing(
        '{{ $open_permintaan->nomor_permintaan }}',
        '{{ addslashes($open_permintaan->jenis_produk) }}',
        {{ $open_permintaan->permintaan_id }}
    );
});
@endisset

</script>
@endpush