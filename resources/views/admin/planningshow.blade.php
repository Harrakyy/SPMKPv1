@extends('layouts.app')

@section('title', 'Detail Planning - ' . $mesin->nama_mesin)

@push('styles')
<style>
    .detail-header-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 20px;
        margin-bottom: 20px;
    }
    .status-active-badge {
        background-color: #d1e7dd;
        color: #0f5132;
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 500;
    }
    .req-badge-inline {
        background-color: #fff3cd;
        color: #856404;
        font-size: 0.75rem;
        padding: 3px 10px;
        border-radius: 4px;
        text-decoration: none;
    }
    .req-badge-inline:hover {
        background-color: #ffeeba;
        color: #856404;
    }
    .doc-item {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .doc-icon {
        width: 40px; height: 40px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex; justify-content: center; align-items: center;
        color: #0d6efd; font-size: 1.2rem;
        margin-right: 15px; flex-shrink: 0;
    }
    .table-custom thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #888;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #ededed;
        font-weight: 600;
        padding: 12px;
    }
    .table-custom tbody td {
        font-size: 0.9rem;
        padding: 14px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #ededed;
    }
    .text-done { color: #198754; font-weight: 600; }
    .text-act  { color: #fd7e14; font-weight: 600; }
    .text-plan { color: #0d6efd; font-weight: 600; }
    .action-icon { cursor: pointer; transition: transform 0.2s; margin-right: 8px; font-size: 1rem; }
    .action-icon:hover { transform: scale(1.15); }
    .icon-edit   { color: #0d6efd; }
    .icon-delete { color: #dc3545; }
    .part-badge {
        background-color: #e0f2fe;
        color: #0284c7;
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 3px;
    }
    #add-activity-card { display: none; border: 1px solid #0d6efd !important; }
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
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        ⚠️ {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-start gap-3">
        <a href="{{ route('admin.planning.index') }}" class="btn btn-light border mt-1 px-2 py-1">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h3 class="fw-bold mb-0">{{ $mesin->nama_mesin }}</h3>
                <span class="status-active-badge">{{ ucfirst($mesin->status) }}</span>
                {{-- ✅ BARU: Tampilkan link ke permintaan terkait --}}
                @if($mesin->permintaan)
                    <a href="{{ route('admin.permintaan.index') }}"
                       class="req-badge-inline">
                        <i class="bi bi-link-45deg"></i>
                        {{ $mesin->permintaan->nomor_permintaan }}
                        — {{ $mesin->permintaan->jenis_produk }}
                    </a>
                @endif
            </div>
            <p class="text-muted mb-0" style="font-size:0.9rem;">
                {{ $mesin->jenis_proses ?? '-' }} &bull; {{ $mesin->lokasi ?? '-' }}
            </p>
        </div>
    </div>
    <button class="btn btn-primary" onclick="toggleAddActivity()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Activity
    </button>
</div>

{{-- MACHINE DOCUMENT CARD --}}
<div class="detail-header-card">
    <h6 class="fw-bold mb-3" style="font-size:0.9rem;">Machine Document</h6>

    <div class="doc-item" id="doc-container"
         style="{{ $mesin->dokumen_path ? '' : 'display:none;' }}">
        <div class="d-flex align-items: center">
            <div class="doc-icon"><i class="bi bi-file-earmark-image"></i></div>
            <div>
                <div class="fw-bold text-dark" style="font-size:0.9rem;" id="doc-file-name">
                    {{ $mesin->dokumen_path ? basename($mesin->dokumen_path) : '' }}
                </div>
                <div class="text-muted" style="font-size:0.75rem;" id="doc-file-info">
                    @if($mesin->dokumen_path)
                        {{ strtoupper(pathinfo($mesin->dokumen_path, PATHINFO_EXTENSION)) }}
                        &bull; Uploaded: {{ $mesin->updated_at?->format('d/m/Y') ?? '-' }}
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm px-3" onclick="viewDocument()">
                <i class="bi bi-eye me-1"></i> View
            </button>
            <button class="btn btn-outline-secondary btn-sm px-3"
                    onclick="document.getElementById('hidden-file-input').click()">
                <i class="bi bi-upload me-1"></i> Replace
            </button>
            <button class="btn btn-outline-danger btn-sm px-3"
                    data-bs-toggle="modal" data-bs-target="#confirmRemoveDocModal">
                <i class="bi bi-trash me-1"></i> Remove
            </button>
        </div>
    </div>

    <input type="file" id="hidden-file-input" style="display:none;"
           accept="image/png,image/jpeg,image/jpg"
           onchange="handleReplaceDocument(event)">

    <div id="empty-doc-message"
         style="{{ $mesin->dokumen_path ? 'display:none;' : '' }}"
         class="text-center text-muted p-3">
        Belum ada dokumen.
        <a href="#" class="text-decoration-none"
           onclick="event.preventDefault(); document.getElementById('hidden-file-input').click()">
            Upload sekarang
        </a>
    </div>
</div>

{{-- FORM TAMBAH ACTIVITY --}}
<div class="detail-header-card" id="add-activity-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0" style="font-size:0.95rem;">Tambah Activity Baru</h6>
        <button type="button" class="btn-close" style="font-size:0.7rem;"
                onclick="toggleAddActivity()"></button>
    </div>
    <form action="{{ route('admin.planning.store') }}" method="POST">
        @csrf
        <input type="hidden" name="mesin_id" value="{{ $mesin->mesin_id }}">
        <div class="row g-3 mb-3">
            <div class="col-12">
                <label class="form-label" style="font-size:0.85rem;">
                    Kode Activity <span class="text-danger">*</span>
                </label>
                <select name="nama_activity" class="form-select form-select-sm" required>
                    <option value="" disabled selected>-- Pilih Kode Activity --</option>
                    <option value="MC">MC</option>
                    <option value="LC">LC</option>
                    <option value="CG">CG</option>
                    <option value="SG">SG</option>
                    <option value="WC">WC</option>
                    <option value="WELD">WELD</option>
                    <option value="H.T">H.T</option>
                    <option value="TAP">TAP</option>
                    <option value="S.T">S.T</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:0.85rem;">
                    PIC / Operator <span class="text-danger">*</span>
                </label>
                <input type="text" name="pic"
                       class="form-control form-control-sm"
                       placeholder="Nama operator" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:0.85rem;">
                    Tanggal Plan <span class="text-danger">*</span>
                </label>
                <input type="date" name="tanggal_plan"
                       class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:0.85rem;">
                    Kode Mesin <span class="text-danger">*</span>
                </label>
                <input type="text" name="request_id"
                    class="form-control form-control-sm"
                    value="{{ $mesin->kode_mesin ?? '' }}"
                    readonly>
            </div>

            {{-- ✅ BARU: Dropdown part list dari permintaan terkait --}}
            <div class="col-md-6">
                <label class="form-label" style="font-size:0.85rem;">
                    Part yang Dikerjakan <span class="text-danger">*</span>
                    @if($partLists->isEmpty())
                        <span class="text-muted" style="font-size:0.75rem;">(tidak ada part terkait)</span>
                    @endif
                </label>
                <select name="partlist_id" class="form-select form-select-sm"
                        {{ $partLists->isEmpty() ? 'disabled' : '' }}>
                    <option value="">-- Pilih Part (opsional) --</option>
                    @foreach($partLists as $part)
                        <option value="{{ $part->part_list_id }}">
                            {{ $part->nama_part }}
                            @if($part->material) — {{ $part->material }} @endif
                            @if($part->dimensi) ({{ $part->dimensi }} @endif
                            @if($part->purchase) {{ $part->purchase }}) @endif
                        </option>
                    @endforeach
                </select>
                @if($partLists->isEmpty() && $mesin->permintaan_id)
                    <div class="form-text text-warning" style="font-size:0.75rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Permintaan terkait belum memiliki part list.
                        <a href="{{ route('admin.permintaan.index') }}">Tambahkan part</a> terlebih dahulu.
                    </div>
                @elseif(!$mesin->permintaan_id)
                    <div class="form-text" style="font-size:0.75rem;">
                        Mesin ini belum terkait dengan permintaan.
                    </div>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Activity</button>
            <button type="button" class="btn btn-light btn-sm px-4 border"
                    onclick="toggleAddActivity()">Batal</button>
        </div>
    </form>
</div>

{{-- TABEL ACTIVITY --}}
<div class="detail-header-card p-0" style="overflow:hidden;">
    <div class="px-4 pt-4 pb-2">
        <h6 class="fw-bold mb-0" style="font-size:0.9rem;">Daftar Activity</h6>
    </div>
    <table class="table table-custom mb-0 px-4" style="width:100%;">
        <thead>
            <tr>
                <th class="ps-4">NO</th>
                <th>NAMA ACTIVITY</th>
                <th>PART</th>
                <th>PIC</th>
                <th>TANGGAL PLAN</th>
                <th>TANGGAL ACTUAL</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proses as $item)
            <tr id="act-row-{{ $item->proses_id }}">
                <td class="ps-4 text-primary fw-bold">
                    {{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}
                </td>
                <td style="max-width:180px;">{{ $item->proses_nama }}</td>

                {{-- ✅ BARU: Tampilkan nama part yang dikerjakan --}}
                <td>
                    @if($item->partList)
                        <span class="part-badge">
                            {{ $item->partList->nama_part }}
                        </span>
                    @else
                        <span class="text-muted" style="font-size:0.8rem;">-</span>
                    @endif
                </td>

                <td>{{ $item->pic ?? '-' }}</td>
                <td>{{ $item->tanggal_plan ? \Carbon\Carbon::parse($item->tanggal_plan)->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->tanggal_actual ? \Carbon\Carbon::parse($item->tanggal_actual)->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($item->status === 'completed')
                        <span class="text-done">Done</span>
                    @elseif($item->status === 'running')
                        <span class="text-act">Running</span>
                    @elseif($item->status === 'pending')
                        <span class="text-plan">Plan</span>
                    @else
                        <span class="text-muted">{{ ucfirst($item->status) }}</span>
                    @endif
                </td>
                <td>
                    <i class="bi bi-pencil action-icon icon-edit" title="Edit"
                       onclick="openEditActivity(
                           {{ $item->mfg_id }},
                           '{{ addslashes($item->proses_nama) }}',
                           '{{ addslashes($item->pic ?? '') }}',
                           '{{ $item->tanggal_plan }}'
                       )"></i>
                    <i class="bi bi-trash action-icon icon-delete" title="Hapus"
                       onclick="confirmDeleteActivity({{ $item->mfg_id }})"></i>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    Belum ada activity. Klik <strong>Tambah Activity</strong> untuk memulai.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL: View Dokumen --}}
<div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <div class="d-flex gap-2 bg-dark rounded-pill p-1">
                    <button class="btn btn-dark btn-sm rounded-circle"
                            style="width:35px;height:35px;" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body text-center p-0 mt-2">
                <img id="modal-preview-image"
                     src="{{ $mesin->dokumen_path
                         ? asset('storage/'.$mesin->dokumen_path)
                         : 'https://via.placeholder.com/1000x600/ffffff/333333?text=Tidak+Ada+Dokumen' }}"
                     class="img-fluid rounded shadow-lg" alt="Machine Document">
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Edit Activity --}}
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Edit Activity</h5>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">Update detail activity</p>
                </div>
                <button type="button" class="btn-close mb-auto mt-2" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form id="editActivityForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Activity <span class="text-danger">*</span></label>
                        <input type="text" name="nama_activity" id="edit-act-name"
                               class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIC / Operator <span class="text-danger">*</span></label>
                        <input type="text" name="pic" id="edit-pic"
                               class="form-control" placeholder="Nama operator" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Tanggal Plan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_plan" id="edit-date"
                               class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="button" class="btn btn-light px-4 border"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Update Activity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Hapus Activity --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:12px;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash text-danger fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold">Hapus Activity?</h5>
                <p class="text-muted mb-4" style="font-size:0.85rem;">
                    Data yang dihapus tidak dapat dikembalikan.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4"
                            data-bs-dismiss="modal">Batal</button>
                    <form id="deleteActivityForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Hapus Dokumen --}}
<div class="modal fade" id="confirmRemoveDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:12px;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-circle text-danger fs-1 mb-3 d-block"></i>
                <h5 class="fw-bold">Hapus Dokumen?</h5>
                <p class="text-muted mb-4" style="font-size:0.85rem;">
                    Dokumen ini akan dihapus dari sistem. Anda yakin?
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-3"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger px-3"
                            onclick="removeDocument()" data-bs-dismiss="modal">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleAddActivity() {
        const card = document.getElementById('add-activity-card');
        card.style.display = (card.style.display === 'none' || card.style.display === '')
            ? 'block' : 'none';
        if (card.style.display === 'block') {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function openEditActivity(id, name, pic, date) {
        document.getElementById('edit-act-name').value = name;
        document.getElementById('edit-pic').value      = pic;
        document.getElementById('edit-date').value     = date;
        document.getElementById('editActivityForm').action = '/admin/planning/' + id;
        new bootstrap.Modal(document.getElementById('editActivityModal')).show();
    }

    function confirmDeleteActivity(id) {
        document.getElementById('deleteActivityForm').action = '/admin/planning/' + id;
        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    }

    function viewDocument() {
        new bootstrap.Modal(document.getElementById('viewImageModal')).show();
    }

    function handleReplaceDocument(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const fileSize = (file.size / 1024).toFixed(1) + ' KB';
            const fileExt  = file.name.split('.').pop().toUpperCase();
            const today    = new Date();
            const dateStr  = `${today.getDate()}/${today.getMonth()+1}/${today.getFullYear()}`;
            document.getElementById('doc-file-name').innerText         = file.name;
            document.getElementById('doc-file-info').innerText         = `${fileSize} • ${fileExt} • Uploaded: ${dateStr}`;
            document.getElementById('modal-preview-image').src         = e.target.result;
            document.getElementById('doc-container').style.display     = 'flex';
            document.getElementById('empty-doc-message').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function removeDocument() {
        document.getElementById('doc-container').style.display     = 'none';
        document.getElementById('empty-doc-message').style.display = 'block';
        document.getElementById('modal-preview-image').src =
            'https://via.placeholder.com/1000x600/ffffff/333333?text=Tidak+Ada+Dokumen';
    }
</script>
@endpush