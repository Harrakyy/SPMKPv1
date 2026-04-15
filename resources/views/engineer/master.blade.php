@extends('layouts.app')

@section('title', 'Master Schedule - PT Sumber Prima Mandiri')

@section('content')

{{-- VIEW: LIST --}}
<div id="view-schedule-list" class="view-section active">
    <div class="mb-4">
        <h2 class="page-title">Master Schedule</h2>
        <p class="page-subtitle">View-Only Monitoring Table - Source: Excel MASTER SCHEDULE</p>
    </div>

    <div class="alert alert-info-custom d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-info-circle fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">View-Only Monitoring</div>
            <div>Jadwal Utama ini hanya untuk memantau kemajuan produksi. Klik baris mana pun untuk melihat detail aktivitas.</div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-blue mb-3"><i class="bi bi-calendar-event"></i></div>
                <div class="stat-value">2</div>
                <div class="stat-label">Total Activities</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-blue mb-3"><i class="bi bi-clock"></i></div>
                <div class="stat-value">0</div>
                <div class="stat-label">Plan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-orange mb-3"><i class="bi bi-arrow-repeat"></i></div>
                <div class="stat-value" style="color:#B54708;">2</div>
                <div class="stat-label">Act</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-green mb-3"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value" style="color:#027A48;">0</div>
                <div class="stat-label">Done</div>
            </div>
        </div>
    </div>

    <div class="content-card mt-0">
        <div class="mb-3">
            <h5 class="fw-bold fs-6">Master Schedule - Activity Monitoring</h5>
            <p class="text-muted small">Click row to view details</p>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Aktivitas Kode</th>
                        <th>Sub Aktivitas</th>
                        <th>Plan</th>
                        <th>Aktual</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="clickable-row" onclick="showDetail('MC')">
                        <td><span class="badge-code">MC</span></td>
                        <td class="fw-medium">CNC Milling 01 - Gear Shaft</td>
                        <td>25/1/2025</td>
                        <td>25/1/2025</td>
                        <td><span class="status-pill status-act">Act</span></td>
                    </tr>
                    <tr class="clickable-row" onclick="showDetail('HT')">
                        <td><span class="badge-code">HT</span></td>
                        <td class="fw-medium">Heat Treatment Furnace - Gear Shaft</td>
                        <td>26/1/2025</td>
                        <td>26/1/2025</td>
                        <td><span class="status-pill status-act">Act</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-warning-custom d-flex align-items-start gap-3 mt-4">
        <i class="bi bi-exclamation-circle fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">Admin Permissions</div>
            <div>Admin hanya dapat melihat jadwal ini. Tidak dapat membuat, mengedit, atau memperbarui status aktivitas.</div>
        </div>
    </div>
</div>

{{-- VIEW: DETAIL --}}
<div id="view-schedule-detail" class="view-section">
    <div class="back-link" onclick="showList()">
        <i class="bi bi-arrow-left"></i> Back to Master Schedule
    </div>
    <div class="mb-4">
        <h2 class="page-title">Master Schedule Detail</h2>
        <div class="d-flex gap-3 text-muted small align-items-center">
            <span>Activity Code: <span class="fw-bold text-dark" id="detail-code">MC</span></span>
            <span>•</span>
            <span>Machine: <span class="fw-bold text-dark" id="detail-machine">CNC Milling 01</span></span>
        </div>
    </div>

    <div class="content-card mt-0 mb-4">
        <div class="mb-4 border-bottom pb-3">
            <h5 class="fw-bold fs-6 mb-1">Activity Information</h5>
            <p class="text-muted small mb-0">Source: Excel MASTER SCHEDULE</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="detail-label">Aktivitas Kode</div>
                <div><span class="badge-code" id="info-code">MC</span></div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Sub Aktivitas</div>
                <div class="detail-value fw-medium" id="info-activity">CNC Milling 01 - Gear Shaft</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">PIC</div>
                <div class="detail-value">Operator A</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Plan Date</div>
                <div class="detail-value">25/1/2025</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Actual Date</div>
                <div class="detail-value">25/1/2025</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Status</div>
                <div><span class="status-pill status-act">Act</span></div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning-custom d-flex align-items-start gap-3 mt-4">
        <i class="bi bi-exclamation-circle fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">Admin Permissions</div>
            <div>Admin hanya dapat melihat jadwal ini. Semua pembaruan status dilakukan oleh Operator.</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showList() {
        document.getElementById('view-schedule-detail').classList.remove('active');
        document.getElementById('view-schedule-list').classList.add('active');
    }

    function showDetail(code) {
        const data = activities[code];
        document.getElementById('detail-code').textContent = data.code;
        document.getElementById('detail-machine').textContent = data.machine;
        document.getElementById('info-code').textContent = data.code;
        document.getElementById('info-activity').textContent = data.activity;
        document.getElementById('view-schedule-list').classList.remove('active');
        document.getElementById('view-schedule-detail').classList.add('active');
        window.scrollTo(0, 0);
    }

    function showList() {
        document.getElementById('view-schedule-detail').classList.remove('active');
        document.getElementById('view-schedule-list').classList.add('active');
    }
</script>
@endpush