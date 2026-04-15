@extends('layouts.app')

@section('title', 'Production Planning')

@push('styles')
<style>
    .status-active-badge {
        background-color: #d1e7dd;
        color: #0f5132;
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 4px;
    }
    .machine-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 20px;
        cursor: pointer;
        transition: box-shadow 0.2s, transform 0.2s;
        height: 100%;
        text-decoration: none;
        display: block;
        color: inherit;
    }
    .machine-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transform: translateY(-2px);
        color: inherit;
    }
    .activity-badge {
        background-color: #cfe2f3;
        color: #084298;
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 4px;
    }
    .req-badge {
        background-color: #fff3cd;
        color: #856404;
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 4px;
        margin-top: 4px;
        display: inline-block;
    }
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        font-size: 0.85rem;
    }
    .dot-plan { color: #0d6efd; }
    .dot-act  { color: #fd7e14; }
    .dot-done { color: #198754; }
    .add-machine-card {
        border: 2px dashed #ccc;
        background: transparent;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #666;
        cursor: pointer;
        min-height: 200px;
        border-radius: 12px;
        padding: 20px;
        transition: 0.2s;
        height: 100%;
    }
    .add-machine-card:hover {
        border-color: #2563eb;
        color: #2563eb;
        background-color: rgba(37,99,235,0.02);
    }
    .add-icon-circle {
        width: 44px;
        height: 44px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: #2563eb;
        transition: 0.2s;
    }
    .add-machine-card:hover .add-icon-circle {
        background-color: #dbeafe;
    }
    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 28px;
        text-align: center;
        background-color: #fafafa;
        cursor: pointer;
        transition: 0.3s;
    }
    .upload-area:hover {
        border-color: #2563eb;
        background-color: #f0f8ff;
    }
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

<div class="mb-4">
    <h3 class="fw-bold mb-1">Production Planning</h3>
    <p class="text-muted">Kelola jadwal activity berdasarkan mesin produksi</p>
</div>

<div class="row g-4">

    @forelse($mesins as $mesin)
    <div class="col-md-4">
        <a href="{{ route('admin.planning.show', $mesin->mesin_id) }}" class="machine-card">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <h6 class="fw-bold mb-0">{{ $mesin->nama_mesin }}</h6>
                <span class="status-active-badge">{{ ucfirst($mesin->status) }}</span>
            </div>

            {{-- Badge permintaan terkait --}}
            @if($mesin->permintaan)
                <span class="req-badge">
                    <i class="bi bi-link-45deg"></i>
                    {{ $mesin->permintaan->nomor_permintaan }}
                </span>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <div>
                    <div style="font-size:0.8rem;color:#666;">{{ $mesin->jenis_proses ?? '-' }}</div>
                    <div style="font-size:0.75rem;color:#999;">{{ $mesin->lokasi ?? '-' }}</div>
                </div>
                <span class="activity-badge">{{ $mesin->total_activity }} Activity</span>
            </div>
            <div class="stat-row">
                <span class="text-muted"><span class="dot-plan">●</span> Plan:</span>
                <span class="fw-bold text-primary">{{ $mesin->plan_count }}</span>
            </div>
            <div class="stat-row">
                <span class="text-muted"><span class="dot-act">●</span> Act:</span>
                <span class="fw-bold" style="color:#fd7e14;">{{ $mesin->act_count }}</span>
            </div>
            <div class="stat-row">
                <span class="text-muted"><span class="dot-done">●</span> Done:</span>
                <span class="fw-bold text-success">{{ $mesin->done_count }}</span>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Belum ada mesin. Tambahkan mesin pertama.
        </div>
    </div>
    @endforelse

    {{-- Tombol Add New Machine --}}
    <div class="col-md-4">
        <div class="add-machine-card" data-bs-toggle="modal" data-bs-target="#addMachineModal">
            <div class="add-icon-circle">
                <i class="bi bi-plus-lg"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Add New Machine</h6>
            <span style="font-size:0.78rem;">Configure new production machine</span>
        </div>
    </div>

</div>


{{-- ===== MODAL ADD NEW MACHINE ===== --}}
<div class="modal fade" id="addMachineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold">Add New Machine</h5>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        Register a new machine to the production system
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form action="{{ route('admin.mesin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.85rem;">
                            Machine Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_mesin" class="form-control"
                               placeholder="e.g., Mesin CNC-02" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.85rem;">
                            Machine Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode_mesin" class="form-control"
                               placeholder="e.g., CNC-02" required>
                        <div class="form-text" style="font-size:0.75rem;">
                            Unique identifier for this machine
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.85rem;">
                            Area / Location <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="lokasi" class="form-control"
                               placeholder="e.g., Area A - Machining" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.85rem;">
                            Machine Type <span class="text-danger">*</span>
                        </label>
                        <select name="jenis_proses" class="form-select" required>
                            <option value="" disabled selected>Select machine type</option>
                            <option value="CNC Milling">CNC Milling</option>
                            <option value="CNC Lathe">CNC Lathe</option>
                            <option value="Laser Cutting">Laser Cutting</option>
                            <option value="Milling Konvensional">Milling Konvensional</option>
                            <option value="Bubut Konvensional">Bubut Konvensional</option>
                            <option value="Grinding">Grinding</option>
                            <option value="Drilling">Drilling</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- ✅ BARU: Dropdown permintaan yang sudah approved --}}
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:0.85rem;">
                            Terkait Permintaan
                        </label>
                        <select name="permintaan_id" class="form-select">
                            <option value="">-- Tidak terkait permintaan --</option>
                            @foreach(\App\Models\Permintaan::whereIn('status', ['approved', 'in_progress'])->orderBy('nomor_permintaan')->get() as $p)
                                <option value="{{ $p->permintaan_id }}">
                                    {{ $p->nomor_permintaan }} — {{ $p->jenis_produk }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size:0.75rem;">
                            Pilih jika mesin ini untuk mengerjakan permintaan tertentu.
                            Hanya permintaan berstatus <strong>Approved</strong> yang tampil.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted mb-1" style="font-size:0.85rem;">
                            Machine Document
                        </label>
                        <div class="form-text mb-2" style="font-size:0.75rem;">
                            Upload foto mesin, layout, atau file spesifikasi (opsional).
                        </div>
                        <div class="upload-area" onclick="document.getElementById('machine-doc-input').click()">
                            <i class="bi bi-upload fs-3 text-secondary mb-2 d-block" id="upload-icon"></i>
                            <div class="fw-bold text-dark" id="upload-text">
                                Drag & drop file here, or click to browse
                            </div>
                            <div class="text-muted mt-1" style="font-size:0.75rem;" id="upload-subtext">
                                Supported: PDF, JPG, PNG (max 10MB)
                            </div>
                        </div>
                        <input type="file" id="machine-doc-input" name="dokumen"
                               style="display:none;" accept=".pdf,.jpg,.jpeg,.png"
                               onchange="handleFileUpload(event)">
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-light px-4 border"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Machine
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        const fileSize = (file.size / 1024).toFixed(1);
        const fileExt  = file.name.split('.').pop().toUpperCase();
        document.getElementById('upload-icon').className = 'bi bi-file-earmark-check fs-3 text-success mb-2 d-block';
        document.getElementById('upload-text').textContent = file.name;
        document.getElementById('upload-text').style.color = '#198754';
        document.getElementById('upload-subtext').textContent = fileSize + ' KB • ' + fileExt + ' — Siap diupload';
    }
</script>
@endpush