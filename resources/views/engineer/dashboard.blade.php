@extends('layouts.app')

@section('title', 'Design Dashboard - PT Sumber Prima Mandiri')

@section('content')
<div class="page-header mb-4">
    <h2 class="page-title">Design Dashboard</h2>
    <p class="page-subtitle">Engineering & BOM Management — {{ now()->translatedFormat('d F Y') }}</p>
</div>

{{-- STAT CARDS --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-title">Permintaan Baru</div>
                <div class="stat-value">{{ $permintaan_baru->count() }}</div>
            </div>
            <div class="icon-box icon-blue"><i class="bi bi-file-earmark-text"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-title">Mesin Maintenance</div>
                <div class="stat-value">{{ $mesin_maintenance->count() }}</div>
            </div>
            <div class="icon-box icon-yellow"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-title">Part Saya</div>
                <div class="stat-value">{{ auth()->user()->designedParts()->count() }}</div>
            </div>
            <div class="icon-box icon-green"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
</div>

{{-- REQUEST LIST --}}
<div class="content-card">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Request List</h5>
        <p class="text-muted small">Semua permintaan produksi yang masuk (status: submitted)</p>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="requestTable">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Nama Permintaan</th>
                    <th>Pemohon</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permintaan_baru as $p)
                <tr>
                    <td><a href="#" class="request-id">{{ $p->kode_permintaan ?? 'REQ-'.$p->permintaan_id }}</a></td>
                    <td>{{ $p->nama_permintaan ?? '-' }}</td>
                    <td>{{ $p->user->nama ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_permintaan)->format('d M Y') }}</td>
                    <td><span class="status-badge status-blue">{{ $p->status }}</span></td>
                    <td><a href="{{ route('engineer.parts.show', $p->permintaan_id) }}" class="action-link">Lihat Detail</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada permintaan baru saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MESIN MAINTENANCE --}}
@if($mesin_maintenance->isNotEmpty())
<div class="content-card">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">⚠️ Mesin dalam Maintenance</h5>
        <p class="text-muted small">Mesin yang sedang tidak beroperasi</p>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Kode Mesin</th>
                    <th>Nama Mesin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mesin_maintenance as $mesin)
                <tr>
                    <td>{{ $mesin->kode_mesin ?? '-' }}</td>
                    <td>{{ $mesin->nama_mesin ?? '-' }}</td>
                    <td><span class="status-badge status-yellow">Maintenance</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        let input = this.value.toLowerCase();
        document.querySelectorAll('#requestTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
        });
    });
</script>
@endpush