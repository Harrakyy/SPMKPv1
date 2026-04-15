@extends('layouts.app')

@section('title', 'Master Schedule')

@section('content')

{{-- VIEW: LIST --}}
<div id="view-schedule-list" class="view-section active">
    <div class="mb-4">
        <h2 class="page-title">Master Schedule</h2>
        <p class="page-subtitle">View-Only Monitoring Table - Source: Database Schedule</p>
    </div>

    <div class="alert alert-info-custom d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-info-circle fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">View-Only Monitoring</div>
            <div>Jadwal Utama ini hanya untuk memantau kemajuan produksi. Klik baris mana pun untuk melihat detail aktivitas.</div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    @php
        $total      = $schedules->count();
        $planCount  = $schedules->where('status', 'pending')->count();
        $actCount   = $schedules->where('status', 'in_progress')->count();
        $doneCount  = $schedules->where('status', 'completed')->count();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-blue mb-3"><i class="bi bi-calendar-event"></i></div>
                <div class="stat-value">{{ $total }}</div>
                <div class="stat-label">Total Activities</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-blue mb-3"><i class="bi bi-clock"></i></div>
                <div class="stat-value">{{ $planCount }}</div>
                <div class="stat-label">Plan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-orange mb-3"><i class="bi bi-arrow-repeat"></i></div>
                <div class="stat-value" style="color:#B54708;">{{ $actCount }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card flex-column align-items-start">
                <div class="stat-icon bg-icon-green mb-3"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value" style="color:#027A48;">{{ $doneCount }}</div>
                <div class="stat-label">Done</div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="content-card mt-0">
        <div class="mb-3">
            <h5 class="fw-bold fs-6">Master Schedule - Activity Monitoring</h5>
            <p class="text-muted small">Click row to view details</p>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mesin</th>
                        <th>Part / Activity</th>
                        <th>PIC</th>
                        <th>Plan Date</th>
                        <th>Actual Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $i => $schedule)
                    <tr class="clickable-row" onclick="showDetail({{ $schedule->schedule_id }})">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="badge-code">
                                {{ $schedule->mesin->kode_mesin ?? '-' }}
                            </span>
                        </td>
                        <td class="fw-medium">
                            {{ $schedule->partList->nama_part ?? '-' }}
                            @if($schedule->activity)
                                <br><small class="text-muted">{{ $schedule->activity }}</small>
                            @endif
                        </td>
                        <td>{{ $schedule->picUser->name ?? '-' }}</td>
                        <td>
                            {{ $schedule->tanggal_plan
                                ? \Carbon\Carbon::parse($schedule->tanggal_plan)->format('d/m/Y')
                                : '-' }}
                        </td>
                        <td>
                            {{ $schedule->tanggal_act
                                ? \Carbon\Carbon::parse($schedule->tanggal_act)->format('d/m/Y')
                                : '-' }}
                        </td>
                        <td>
                            @if($schedule->status === 'completed')
                                <span class="status-pill status-done">Done</span>
                            @elseif($schedule->status === 'in_progress')
                                <span class="status-pill status-act">Act</span>
                            @else
                                <span class="status-pill status-plan">Plan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Belum ada schedule tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-warning-custom d-flex align-items-start gap-3 mt-4">
        <i class="bi bi-exclamation-circle fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">View Only</div>
            <div>Halaman ini hanya untuk memantau jadwal. Semua pembaruan status dilakukan melalui halaman Schedule.</div>
        </div>
    </div>
</div>

{{-- VIEW: DETAIL --}}
<div id="view-schedule-detail" class="view-section">
    <div class="back-link" onclick="showList()" style="cursor:pointer;">
        <i class="bi bi-arrow-left"></i> Back to Master Schedule
    </div>
    <div class="mb-4">
        <h2 class="page-title">Master Schedule Detail</h2>
        <div class="d-flex gap-3 text-muted small align-items-center">
            <span>Mesin: <span class="fw-bold text-dark" id="detail-machine">-</span></span>
            <span>•</span>
            <span>Part: <span class="fw-bold text-dark" id="detail-part">-</span></span>
        </div>
    </div>

    <div class="content-card mt-0 mb-4">
        <div class="mb-4 border-bottom pb-3">
            <h5 class="fw-bold fs-6 mb-1">Activity Information</h5>
            <p class="text-muted small mb-0">Detail schedule dari database</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="detail-label">Mesin</div>
                <div><span class="badge-code" id="info-mesin">-</span></div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Part</div>
                <div class="detail-value fw-medium" id="info-part">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Activity</div>
                <div class="detail-value" id="info-activity">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">PIC</div>
                <div class="detail-value" id="info-pic">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Plan Date</div>
                <div class="detail-value" id="info-plan-date">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Actual Date</div>
                <div class="detail-value" id="info-act-date">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Start Time (Plan)</div>
                <div class="detail-value" id="info-start-plan">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">End Time (Plan)</div>
                <div class="detail-value" id="info-end-plan">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Durasi Plan</div>
                <div class="detail-value" id="info-durasi-plan">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Start Time (Actual)</div>
                <div class="detail-value" id="info-start-act">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">End Time (Actual)</div>
                <div class="detail-value" id="info-end-act">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Durasi Actual</div>
                <div class="detail-value" id="info-durasi-act">-</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Status</div>
                <div id="info-status">-</div>
            </div>
            <div class="col-md-8">
                <div class="detail-label">Catatan</div>
                <div class="detail-value" id="info-catatan">-</div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning-custom d-flex align-items-start gap-3 mt-4">
        <i class="bi bi-exclamation-circle fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">View Only</div>
            <div>Semua pembaruan status dilakukan oleh Operator melalui halaman Schedule.</div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Data dari PHP ke JS
    const scheduleData = {
        @foreach($schedules as $schedule)
        {{ $schedule->schedule_id }}: {
            mesin       : "{{ $schedule->mesin->kode_mesin ?? '-' }}",
            mesinNama   : "{{ $schedule->mesin->nama_mesin ?? '-' }}",
            part        : "{{ $schedule->partList->nama_part ?? '-' }}",
            activity    : "{{ $schedule->activity ?? '-' }}",
            pic         : "{{ $schedule->picUser->name ?? '-' }}",
            planDate    : "{{ $schedule->tanggal_plan ? \Carbon\Carbon::parse($schedule->tanggal_plan)->format('d/m/Y') : '-' }}",
            actDate     : "{{ $schedule->tanggal_act ? \Carbon\Carbon::parse($schedule->tanggal_act)->format('d/m/Y') : '-' }}",
            startPlan   : "{{ $schedule->start_time_plan ?? '-' }}",
            endPlan     : "{{ $schedule->end_time_plan ?? '-' }}",
            durasiPlan  : "{{ $schedule->durasi_plan_minutes ? $schedule->durasi_plan_minutes . ' menit' : '-' }}",
            startAct    : "{{ $schedule->start_time_act ?? '-' }}",
            endAct      : "{{ $schedule->end_time_act ?? '-' }}",
            durasiAct   : "{{ $schedule->durasi_act_minutes ? $schedule->durasi_act_minutes . ' menit' : '-' }}",
            status      : "{{ $schedule->status }}",
            catatan     : "{{ $schedule->catatan ?? '-' }}",
        },
        @endforeach
    };

    function statusBadge(status) {
        if (status === 'completed') return '<span class="status-pill status-done">Done</span>';
        if (status === 'in_progress') return '<span class="status-pill status-act">Act</span>';
        return '<span class="status-pill status-plan">Plan</span>';
    }

    function showDetail(id) {
        const d = scheduleData[id];
        if (!d) return;

        document.getElementById('detail-machine').textContent = d.mesinNama;
        document.getElementById('detail-part').textContent    = d.part;
        document.getElementById('info-mesin').textContent     = d.mesin;
        document.getElementById('info-part').textContent      = d.part;
        document.getElementById('info-activity').textContent  = d.activity;
        document.getElementById('info-pic').textContent       = d.pic;
        document.getElementById('info-plan-date').textContent = d.planDate;
        document.getElementById('info-act-date').textContent  = d.actDate;
        document.getElementById('info-start-plan').textContent= d.startPlan;
        document.getElementById('info-end-plan').textContent  = d.endPlan;
        document.getElementById('info-durasi-plan').textContent= d.durasiPlan;
        document.getElementById('info-start-act').textContent = d.startAct;
        document.getElementById('info-end-act').textContent   = d.endAct;
        document.getElementById('info-durasi-act').textContent= d.durasiAct;
        document.getElementById('info-status').innerHTML      = statusBadge(d.status);
        document.getElementById('info-catatan').textContent   = d.catatan;

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