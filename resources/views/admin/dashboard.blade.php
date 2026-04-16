@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Admin Dashboard</h3>
        <p class="text-muted">Gambaran umum pemantauan & pengendalian produksi — {{ now()->translatedFormat('d F Y') }}</p>
    </div>

    {{-- ── SECTION: PRODUCTION TRACKING ──────────────────────────────────── --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Production Tracking</h5>
            <small class="text-muted">Klik mesin untuk melihat detail tracking progress</small>
        </div>
        <a href="{{ route('admin.planning.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-grid me-1"></i> Lihat Planning
        </a>
    </div>

    {{-- ── MACHINE CARDS (mirip planning.blade.php) ────────────────────────── --}}
    <div class="row g-3 mb-4">
        @forelse($data['mesins'] as $mesin)
        <div class="col-md-4">
            <div class="machine-track-card" onclick="toggleTrackingTable('{{ $mesin['mesin_id'] }}')"
                 id="card-{{ $mesin['mesin_id'] }}">

                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="fw-bold mb-0">{{ $mesin['nama_mesin'] }}</h6>
                    <span class="status-active-badge
                        {{ $mesin['status'] === 'active' ? 'badge-active' :
                           ($mesin['status'] === 'maintenance' ? 'badge-maint' : 'badge-inactive') }}">
                        {{ ucfirst($mesin['status']) }}
                    </span>
                </div>

                @if($mesin['nomor_permintaan'] !== '-')
                    <span class="req-badge">
                        <i class="bi bi-link-45deg"></i>
                        {{ $mesin['nomor_permintaan'] }}
                    </span>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                    <div>
                        <div style="font-size:0.8rem;color:#666;">{{ $mesin['jenis_proses'] ?? '-' }}</div>
                        <div style="font-size:0.75rem;color:#999;">{{ $mesin['lokasi'] ?? '-' }}</div>
                    </div>
                    <span class="activity-badge">{{ $mesin['total_activity'] }} Activity</span>
                </div>

                {{-- Progress bar mini --}}
                @php
                    $total = max($mesin['total_activity'], 1);
                    $pct   = round(($mesin['done_count'] / $total) * 100);
                @endphp
                <div class="progress mb-2" style="height:4px;border-radius:4px;">
                    <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                </div>

                <div class="d-flex justify-content-between" style="font-size:0.78rem;color:#888;">
                    <span><span style="color:#0d6efd;">●</span> Plan: <strong class="text-primary">{{ $mesin['plan_count'] }}</strong></span>
                    <span><span style="color:#fd7e14;">●</span> Act: <strong style="color:#fd7e14;">{{ $mesin['act_count'] }}</strong></span>
                    <span><span style="color:#198754;">●</span> Done: <strong class="text-success">{{ $mesin['done_count'] }}</strong></span>
                    <span class="ms-auto text-muted">{{ $pct }}%</span>
                </div>

                {{-- Indikator tanggal trial & delivery (ringkasan) --}}
                <div class="d-flex gap-2 mt-2" style="font-size:0.73rem;">
                    <span class="track-pill {{ $mesin['tanggal_trial'] ? 'pill-filled' : 'pill-empty' }}">
                        <i class="bi bi-tools me-1"></i>
                        Trial: {{ $mesin['tanggal_trial'] ? \Carbon\Carbon::parse($mesin['tanggal_trial'])->format('d/m/Y') : '—' }}
                    </span>
                    <span class="track-pill {{ $mesin['tanggal_delivery'] ? 'pill-filled' : 'pill-empty' }}">
                        <i class="bi bi-truck me-1"></i>
                        Delivery: {{ $mesin['tanggal_delivery'] ? \Carbon\Carbon::parse($mesin['tanggal_delivery'])->format('d/m/Y') : '—' }}
                    </span>
                </div>

                {{-- Arrow indicator --}}
                <div class="text-end mt-1">
                    <i class="bi bi-chevron-down card-arrow" id="arrow-{{ $mesin['mesin_id'] }}"
                       style="font-size:0.8rem;color:#aaa;transition:transform 0.25s;"></i>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada mesin terdaftar.
            </div>
        </div>
        @endforelse
    </div>

    {{-- ── TRACKING TABLES (collapsed, muncul saat kartu diklik) ──────────── --}}
    @foreach($data['mesins'] as $mesin)
    <div class="tracking-table-wrapper" id="tracking-{{ $mesin['mesin_id'] }}" style="display:none;">
        <div class="custom-card border-0 mb-4">

            {{-- Header tabel --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-table me-2 text-primary"></i>
                        Tracking: {{ $mesin['nama_mesin'] }}
                        @if($mesin['nomor_permintaan'] !== '-')
                            <span class="req-badge ms-2">{{ $mesin['nomor_permintaan'] }}</span>
                        @endif
                    </h6>
                    <small class="text-muted">{{ $mesin['jenis_produk'] }}</small>
                </div>
                <button class="btn btn-sm btn-light border"
                        onclick="toggleTrackingTable('{{ $mesin['mesin_id'] }}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-sm tracking-tbl mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal PO</th>
                            <th>PO Mekanik</th>
                            <th>PO Elektrikal</th>
                            <th>Assy Mekanik</th>
                            <th>Assy Elektrikal</th>
                            <th>Trial / Install</th>
                            <th>Delivery</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="row-{{ $mesin['mesin_id'] }}">

                            {{-- Tanggal PO (dari tanggal_permintaan) --}}
                            <td>
                                @if($mesin['tanggal_po'])
                                    <span class="date-chip date-chip-blue">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($mesin['tanggal_po'])->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                          {{-- PO Mekanik --}}
                            <td>
                                @if($mesin['po_mekanik_count'] > 0)
                                    <div class="d-flex flex-column gap-1">
                                        <span class="buyer-badge buyer-mek">
                                            <i class="bi bi-wrench me-1"></i>
                                            {{ $mesin['po_mekanik_count'] }} part
                                        </span>
                                        <small class="text-muted" style="font-size:0.7rem; line-height:1.3;">
                                            {{ $mesin['po_mekanik'] }}
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- PO Elektrikal --}}
                            <td>
                                @if($mesin['po_elektrikal_count'] > 0)
                                    <div class="d-flex flex-column gap-1">
                                        <span class="buyer-badge buyer-elek">
                                            <i class="bi bi-lightning me-1"></i>
                                            {{ $mesin['po_elektrikal_count'] }} part
                                        </span>
                                        <small class="text-muted" style="font-size:0.7rem; line-height:1.3;">
                                            {{ $mesin['po_elektrikal'] }}
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Assy Mekanik (tanggal_actual dari schedule) --}}
                            <td>
                                @if($mesin['assy_mekanik'])
                                    <span class="date-chip date-chip-orange">
                                        <i class="bi bi-wrench me-1"></i>
                                        {{ \Carbon\Carbon::parse($mesin['assy_mekanik'])->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Assy Elektrikal (tanggal_actual dari schedule) --}}
                            <td>
                                @if($mesin['assy_elektrikal'])
                                    <span class="date-chip date-chip-purple">
                                        <i class="bi bi-lightning me-1"></i>
                                        {{ \Carbon\Carbon::parse($mesin['assy_elektrikal'])->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Trial / Install (EDITABLE) --}}
                            <td>
                                <div class="editable-date-cell" id="trial-display-{{ $mesin['mesin_id'] }}">
                                    @if($mesin['tanggal_trial'])
                                        <span class="date-chip date-chip-green">
                                            <i class="bi bi-tools me-1"></i>
                                            {{ \Carbon\Carbon::parse($mesin['tanggal_trial'])->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="empty-date-btn"
                                              onclick="openDateEdit('{{ $mesin['mesin_id'] }}', 'trial')">
                                            <i class="bi bi-plus-circle me-1"></i>Set Tanggal
                                        </span>
                                    @endif
                                    <button class="btn-edit-date ms-1"
                                            onclick="openDateEdit('{{ $mesin['mesin_id'] }}', 'trial')"
                                            title="Edit tanggal trial">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                                {{-- Input (hidden) --}}
                                <div class="date-input-wrap" id="trial-input-{{ $mesin['mesin_id'] }}" style="display:none;">
                                    <input type="date" class="form-control form-control-sm"
                                           id="trial-val-{{ $mesin['mesin_id'] }}"
                                           value="{{ $mesin['tanggal_trial'] ?? '' }}">
                                    <div class="d-flex gap-1 mt-1">
                                        <button class="btn btn-xs btn-primary"
                                                onclick="saveDate('{{ $mesin['mesin_id'] }}', 'trial')">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-xs btn-light border"
                                                onclick="cancelDateEdit('{{ $mesin['mesin_id'] }}', 'trial')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            {{-- Delivery (EDITABLE) --}}
                            <td>
                                <div class="editable-date-cell" id="delivery-display-{{ $mesin['mesin_id'] }}">
                                    @if($mesin['tanggal_delivery'])
                                        <span class="date-chip date-chip-teal">
                                            <i class="bi bi-truck me-1"></i>
                                            {{ \Carbon\Carbon::parse($mesin['tanggal_delivery'])->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="empty-date-btn"
                                              onclick="openDateEdit('{{ $mesin['mesin_id'] }}', 'delivery')">
                                            <i class="bi bi-plus-circle me-1"></i>Set Tanggal
                                        </span>
                                    @endif
                                    <button class="btn-edit-date ms-1"
                                            onclick="openDateEdit('{{ $mesin['mesin_id'] }}', 'delivery')"
                                            title="Edit tanggal delivery">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                                <div class="date-input-wrap" id="delivery-input-{{ $mesin['mesin_id'] }}" style="display:none;">
                                    <input type="date" class="form-control form-control-sm"
                                           id="delivery-val-{{ $mesin['mesin_id'] }}"
                                           value="{{ $mesin['tanggal_delivery'] ?? '' }}">
                                    <div class="d-flex gap-1 mt-1">
                                        <button class="btn btn-xs btn-primary"
                                                onclick="saveDate('{{ $mesin['mesin_id'] }}', 'delivery')">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-xs btn-light border"
                                                onclick="cancelDateEdit('{{ $mesin['mesin_id'] }}', 'delivery')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    {{-- ── TABEL GABUNGAN REQUEST & PLANNING ───────────────────────────────── 
    <div class="row mt-2">
        <div class="col-12">
            <div class="custom-card border-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Request & Planning</h5>
                        <small class="text-muted">Data activity terbaru beserta permintaan terkait</small>
                    </div>
                    <a href="{{ route('admin.planning.index') }}"
                       class="text-primary fw-bold text-decoration-none" style="font-size:14px;">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr style="font-size:0.75rem; color:#888; text-transform:uppercase; border-bottom:2px solid #eee;">
                                <th class="ps-3">No. Permintaan</th>
                                <th>Jenis Produk</th>
                                <th>Mesin</th>
                                <th>Nama Activity</th>
                                <th>PIC</th>
                                <th>Tanggal Plan</th>
                                <th>Status Permintaan</th>
                                <th>Status Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['tabel_gabungan'] as $item)
                            @php
                                $permintaan = $item->mesin?->permintaan;
                                $statusReq  = $permintaan?->status ?? null;
                                $badgeReq   = match($statusReq) {
                                    'submitted'   => 'bg-primary',
                                    'approved'    => 'bg-success',
                                    'rejected'    => 'bg-danger',
                                    'in_progress' => 'bg-warning text-dark',
                                    'completed'   => 'bg-success',
                                    default       => 'bg-secondary',
                                };
                            @endphp
                            <tr style="font-size:0.88rem; vertical-align:middle;">
                                <td class="ps-3">
                                    @if($permintaan)
                                        <span class="badge"
                                              style="background:#fff3cd; color:#856404; font-weight:500;">
                                            {{ $permintaan->nomor_permintaan }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $permintaan?->jenis_produk ?? '-' }}</td>
                                <td>{{ $item->mesin?->nama_mesin ?? '-' }}</td>
                                <td>{{ $item->proses_nama }}</td>
                                <td>{{ $item->pic ?? '-' }}</td>
                                <td>
                                    {{ $item->tanggal_plan
                                        ? \Carbon\Carbon::parse($item->tanggal_plan)->format('d/m/Y')
                                        : '-' }}
                                </td>
                                <td>
                                    @if($statusReq)
                                        <span class="badge {{ $badgeReq }}">
                                            {{ ucfirst(str_replace('_', ' ', $statusReq)) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status === 'completed')
                                        <span class="badge bg-success">Done</span>
                                    @elseif($item->status === 'running')
                                        <span class="badge bg-warning text-dark">Running</span>
                                    @else
                                        <span class="badge bg-primary">Plan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    Belum ada data activity.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>--}}

</div>

{{-- Toast Notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div id="saveToast" class="toast align-items-center text-white border-0" role="alert" style="min-width:220px;">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ── Machine Cards ─────────────────────────────────── */
    .machine-track-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 18px;
        cursor: pointer;
        transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
        height: 100%;
    }
    .machine-track-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
        border-color: #bfdbfe;
    }
    .machine-track-card.active-card {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
    }
    .machine-track-card.active-card .card-arrow {
        transform: rotate(180deg);
        color: #0d6efd !important;
    }

    /* ── Status badges ─────────────────────────────────── */
    .status-active-badge {
        font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; font-weight: 500;
    }
    .badge-active   { background:#d1e7dd; color:#0f5132; }
    .badge-maint    { background:#fff3cd; color:#856404; }
    .badge-inactive { background:#f8d7da; color:#842029; }

    .req-badge {
        background:#fff3cd; color:#856404;
        font-size:0.7rem; padding:3px 8px;
        border-radius:4px; display:inline-block;
    }
    .activity-badge {
        background:#cfe2f3; color:#084298;
        font-size:0.7rem; padding:3px 8px; border-radius:4px;
    }

    /* ── Track pill (ringkasan di kartu) ───────────────── */
    .track-pill {
        padding: 2px 8px; border-radius: 20px; font-size: 0.7rem;
    }
    .pill-filled { background:#d1fae5; color:#065f46; }
    .pill-empty  { background:#f1f5f9; color:#94a3b8; border: 1px dashed #cbd5e1; }

    /* ── Tracking Table ────────────────────────────────── */
    .tracking-table-wrapper {
        animation: slideDown 0.25s ease;
    }
    @keyframes slideDown {
        from { opacity:0; transform:translateY(-8px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .tracking-tbl thead th {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: #888;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #eee;
        padding: 10px 12px;
        white-space: nowrap;
        font-weight: 600;
    }
    .tracking-tbl tbody td {
        font-size: 0.88rem;
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    /* ── Date chips ────────────────────────────────────── */
    .date-chip {
        display: inline-flex; align-items: center;
        font-size: 0.78rem; padding: 3px 10px;
        border-radius: 20px; font-weight: 500; white-space: nowrap;
    }
    .date-chip-blue   { background:#dbeafe; color:#1e40af; }
    .date-chip-orange { background:#ffedd5; color:#9a3412; }
    .date-chip-purple { background:#ede9fe; color:#5b21b6; }
    .date-chip-green  { background:#dcfce7; color:#166534; }
    .date-chip-teal   { background:#ccfbf1; color:#134e4a; }

    /* ── Buyer badges ──────────────────────────────────── */
    .buyer-badge {
        display: inline-flex; align-items: center;
        font-size: 0.78rem; padding: 3px 10px;
        border-radius: 20px; font-weight: 500;
    }
    .buyer-mek  { background:#e0f2fe; color:#0369a1; }
    .buyer-elek { background:#fce7f3; color:#9d174d; }

    /* ── Editable date cell ────────────────────────────── */
    .editable-date-cell {
        display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-edit-date {
        background: none; border: none; padding: 2px 4px;
        color: #aaa; font-size: 0.75rem; cursor: pointer;
        opacity: 0; transition: opacity 0.15s;
    }
    .tracking-tbl tbody tr:hover .btn-edit-date { opacity: 1; }
    .btn-edit-date:hover { color: #0d6efd; }

    .empty-date-btn {
        color: #aaa; font-size: 0.78rem;
        cursor: pointer; white-space: nowrap;
        border: 1px dashed #d1d5db; border-radius: 20px;
        padding: 2px 10px; transition: 0.15s;
    }
    .empty-date-btn:hover { color: #0d6efd; border-color: #0d6efd; background: #f0f7ff; }

    .date-input-wrap { min-width: 130px; }

    .btn-xs {
        padding: 2px 8px; font-size: 0.75rem;
        border-radius: 4px; line-height: 1.4;
    }

    /* ── Custom card (existing style) ─────────────────── */
    .custom-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
    // ── Toggle tracking table ──────────────────────────────────────────────
    function toggleTrackingTable(mesinId) {
        const wrapper = document.getElementById('tracking-' + mesinId);
        const card    = document.getElementById('card-' + mesinId);
        const isOpen  = wrapper.style.display !== 'none';

        // Tutup semua dulu
        document.querySelectorAll('.tracking-table-wrapper').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.machine-track-card').forEach(el => {
            el.classList.remove('active-card');
        });

        if (!isOpen) {
            wrapper.style.display = 'block';
            card.classList.add('active-card');
            // Scroll ke tabel
            setTimeout(() => {
                wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 50);
        }
    }

    // ── Open date edit ─────────────────────────────────────────────────────
    function openDateEdit(mesinId, type) {
        document.getElementById(type + '-display-' + mesinId).style.display  = 'none';
        document.getElementById(type + '-input-' + mesinId).style.display    = 'block';
        document.getElementById(type + '-val-' + mesinId).focus();
    }

    // ── Cancel date edit ───────────────────────────────────────────────────
    function cancelDateEdit(mesinId, type) {
        document.getElementById(type + '-input-' + mesinId).style.display   = 'none';
        document.getElementById(type + '-display-' + mesinId).style.display = 'inline-flex';
    }

    // ── Save date via AJAX ─────────────────────────────────────────────────
    function saveDate(mesinId, type) {
        const val = document.getElementById(type + '-val-' + mesinId).value;

        const body = {};
        body['tanggal_' + type] = val;

        fetch(`/admin/dashboard/tracking/${trackingId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(body),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update tampilan chip
                const displayEl = document.getElementById(type + '-display-' + mesinId);
                const formatted = val ? formatDate(val) : '—';
                const icons     = { trial: 'bi-tools', delivery: 'bi-truck' };
                const colors    = { trial: 'date-chip-green', delivery: 'date-chip-teal' };

                if (val) {
                    displayEl.innerHTML = `
                        <span class="date-chip ${colors[type]}">
                            <i class="bi ${icons[type]} me-1"></i>${formatted}
                        </span>
                        <button class="btn-edit-date ms-1" onclick="openDateEdit('${mesinId}', '${type}')" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>`;
                } else {
                    displayEl.innerHTML = `
                        <span class="empty-date-btn" onclick="openDateEdit('${mesinId}', '${type}')">
                            <i class="bi bi-plus-circle me-1"></i>Set Tanggal
                        </span>
                        <button class="btn-edit-date ms-1" onclick="openDateEdit('${mesinId}', '${type}')">
                            <i class="bi bi-pencil"></i>
                        </button>`;
                }

                // Update pill di kartu mesin
                updateCardPill(mesinId, type, val);

                cancelDateEdit(mesinId, type);
                showToast('✅ Tersimpan!', 'bg-success');
            } else {
                showToast('❌ Gagal menyimpan.', 'bg-danger');
            }
        })
        .catch(() => showToast('❌ Terjadi kesalahan.', 'bg-danger'));
    }

    // ── Update pill ringkasan di kartu mesin ──────────────────────────────
    function updateCardPill(mesinId, type, val) {
        const card  = document.getElementById('card-' + mesinId);
        if (!card) return;
        const pills = card.querySelectorAll('.track-pill');
        const idx   = type === 'trial' ? 0 : 1;
        if (!pills[idx]) return;
        const icons = { trial: '🔧', delivery: '🚚' };
        const labels = { trial: 'Trial', delivery: 'Delivery' };
        pills[idx].className = 'track-pill ' + (val ? 'pill-filled' : 'pill-empty');
        pills[idx].innerHTML = `${labels[type]}: ${val ? formatDate(val) : '—'}`;
    }

    // ── Format date dd/mm/yyyy ─────────────────────────────────────────────
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    // ── Toast ──────────────────────────────────────────────────────────────
    function showToast(msg, bgClass) {
        const el = document.getElementById('saveToast');
        el.className = `toast align-items-center text-white border-0 ${bgClass}`;
        document.getElementById('toastMsg').textContent = msg;
        new bootstrap.Toast(el, { delay: 2500 }).show();
    }
</script>
@endpush