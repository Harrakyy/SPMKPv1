@extends('layouts.app')

@section('content')
<style>
    .page-title    { font-size: 24px; font-weight: 600; margin-bottom: 4px; }
    .page-subtitle { color: #6b7280; margin-bottom: 25px; font-size: 14px; }

    /* STAT CARDS */
    .stat-container { display: flex; gap: 25px; margin-bottom: 25px; flex-wrap: wrap; }
    .stat-card {
        background: white; padding: 22px; border-radius: 12px; flex: 1; min-width: 200px;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stat-title  { font-size: 14px; color: #6b7280; }
    .stat-value  { font-size: 32px; font-weight: bold; margin-top: 5px; }
    .stat-icon   { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .icon-blue   { background: #dbeafe; color: #2563eb; }
    .icon-yellow { background: #fef3c7; color: #f59e0b; }
    .icon-green  { background: #d1fae5; color: #16a34a; }

    /* BOX */
    .box        { background: white; padding: 25px; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 25px; }
    .box-title  { font-size: 18px; font-weight: 600; margin-bottom: 15px; }
    .box-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 14px; color: #374151; }
    .status-busy  { background: #fde68a; color: #92400e; padding: 3px 10px; border-radius: 6px; font-size: 12px; display: inline-block; }
    .status-free  { background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 6px; font-size: 12px; display: inline-block; }

    /* DOCUMENT */
    .document-box { border: 1px dashed #d1d5db; padding: 40px; border-radius: 10px; text-align: center; color: #6b7280; }

    /* TABLE */
    .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .view-btn     { background: #2563eb; color: white; padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; }
    table         { width: 100%; border-collapse: collapse; }
    th            { text-align: left; font-size: 13px; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 10px 0; }
    td            { padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
    .status-act   { color: #ef4444; font-weight: 600; }
    .status-done  { color: #16a34a; font-weight: 600; }
    .complete-btn { background: #16a34a; border: none; color: white; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; }
    .complete-btn:hover { background: #15803d; }

    /* Flash */
    .alert { border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
    .alert-danger  { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
</style>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
@endif

<div class="page-title">Operator Dashboard</div>
<div class="page-subtitle">Daily execution overview — {{ now()->translatedFormat('d F Y') }}</div>

{{-- STAT CARDS --}}
<div class="stat-container">
    <div class="stat-card">
        <div>
            <div class="stat-title">Today's Activities</div>
            <div class="stat-value">{{ $tugas_hari_ini->count() }}</div>
        </div>
        <div class="stat-icon icon-blue">
            <i class="bi bi-tools"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Current Activity</div>
            <div class="stat-value">{{ $proses_aktif->count() }}</div>
        </div>
        <div class="stat-icon icon-yellow">
            <i class="bi bi-clock"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Completed Today</div>
            <div class="stat-value">
                {{ $tugas_hari_ini->where('status', 'completed')->count() }}
            </div>
        </div>
        <div class="stat-icon icon-green">
            <i class="bi bi-check"></i>
        </div>
    </div>
</div>

{{-- PROSES AKTIF --}}
@if($proses_aktif->isNotEmpty())
@foreach($proses_aktif as $proses)
<div class="box">
    <div class="box-title">Assigned Machine</div>
    <div class="box-grid">
        <div>Machine : {{ $proses->mesin->nama_mesin ?? '-' }}</div>
        <div>Code    : {{ $proses->mesin->kode_mesin ?? '-' }}</div>
        <div>Type    : {{ $proses->mesin->jenis_mesin ?? '-' }}</div>
        <div>Area    : {{ $proses->mesin->area ?? '-' }}</div>
        <div>
            Status :
            <span class="status-busy">Busy</span>
        </div>
        <div>Part : {{ $proses->partList->nama_part ?? '-' }}</div>
    </div>
    <hr style="margin: 20px 0">
    <div style="font-weight:600; margin-bottom:10px">Machine Document</div>
    <div class="document-box">
        <i class="bi bi-file-earmark" style="font-size: 30px"></i>
        <br><br>No Machine Document uploaded
    </div>
</div>
@endforeach
@endif

{{-- TODAY'S ACTIVITIES --}}
<div class="box">
    <div class="table-header">
        <div>
            <div style="font-weight:600">Today's Activities</div>
            <div style="font-size:13px; color:#6b7280">Your assigned tasks for today</div>
        </div>
        <a href="{{ route('operator.schedule.index') }}" class="view-btn">
            View All Activities
        </a>
    </div>

    <table>
        <tr>
            <th>Activity</th>
            <th>Part Name</th>
            <th>Machine</th>
            <th>Plan Date</th>
            <th>Status</th>
            <th>Quick Action</th>
        </tr>
        @forelse($tugas_hari_ini as $schedule)
        <tr>
            <td>{{ $schedule->nama_activity ?? $schedule->schedule_id }}</td>
            <td>{{ $schedule->partList->nama_part ?? '-' }}</td>
            <td>{{ $schedule->mesin->nama_mesin ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($schedule->tanggal_plan)->format('d M Y') }}</td>
            <td>
                @if($schedule->status === 'in_progress')
                    <span class="status-act">In Progress</span>
                @elseif($schedule->status === 'completed')
                    <span class="status-done">Completed</span>
                @else
                    <span style="color:#6b7280">{{ $schedule->status }}</span>
                @endif
            </td>
            <td>
                @if($schedule->status !== 'completed')
                <form action="{{ route('operator.schedule.complete', $schedule->schedule_id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="complete-btn">Complete</button>
                </form>
                @else
                    <span style="color:#16a34a; font-size:13px">✅ Selesai</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; color:#6b7280; padding:30px 0;">
                Tidak ada aktivitas untuk hari ini.
            </td>
        </tr>
        @endforelse
    </table>
</div>

@endsection