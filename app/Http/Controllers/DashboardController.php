<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Mesin;
use App\Models\ProsesMfg;
use App\Models\Schedule;
use App\Models\PartList;
use App\Models\User;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */
    public function admin()
    {
        $user = auth()->user();

        $data = [
            // ── Statistik Permintaan ──────────────────────────────
            'total_permintaan'      => Permintaan::count(),
            'permintaan_pending'    => Permintaan::where('status', 'submitted')->count(),
            'permintaan_inprogress' => Permintaan::where('status', 'approved')->count(),

            // ── Statistik Mesin ───────────────────────────────────
            'mesin_aktif'       => Mesin::where('status', 'active')->count(),
            'mesin_maintenance' => Mesin::where('status', 'maintenance')->count(),

            // ── Statistik Planning (ProsesMfg) ────────────────────
            'total_activity'   => ProsesMfg::count(),
            'activity_pending' => ProsesMfg::where('status', 'pending')->count(),
            'activity_running' => ProsesMfg::where('status', 'running')->count(),
            'activity_done'    => ProsesMfg::where('status', 'completed')->count(),

            // ── Chart Pie: Status Permintaan ──────────────────────
            'permintaan_by_status' => Permintaan::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),

            // ── Chart Bar: Tren Produksi Bulanan ──────────────────
            'permintaan_by_month' => Permintaan::selectRaw('DATE_FORMAT(tanggal_permintaan, "%Y-%m") as month, count(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->take(6)
                ->pluck('total', 'month')
                ->toArray(),

            // ── Tabel Gabungan Request & Planning ─────────────────
            'tabel_gabungan' => ProsesMfg::with(['mesin.permintaan'])
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('admin.dashboard', compact('data', 'user'));
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD OPERATOR
    |--------------------------------------------------------------------------
    */
    public function operator()
    {
        $user = auth()->user();

        $tugas_hari_ini = Schedule::where('pic', $user->user_id)
            ->whereDate('tanggal_plan', today())
            ->whereIn('status', ['planned', 'in_progress'])
            ->with(['mesin', 'partList'])
            ->get();

        $proses_aktif = ProsesMfg::where('operator_id', $user->user_id)
            ->where('status', 'running')
            ->with(['partList', 'mesin'])
            ->get();

        return view('operator.dashboard', compact('user', 'tugas_hari_ini', 'proses_aktif'));
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ENGINEER
    |--------------------------------------------------------------------------
    */
    public function engineer()
    {
        $user = auth()->user();

        $permintaan_baru = Permintaan::where('status', 'submitted')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $schedules = Schedule::with(['mesin', 'part'])
            ->orderBy('tanggal_plan', 'asc')
            ->take(10)
            ->get();

        $mesin_maintenance = Mesin::where('status', 'maintenance')->get();

        return view('engineer.dashboard', compact('user', 'permintaan_baru', 'schedules', 'mesin_maintenance'));
    }

    /*
    |--------------------------------------------------------------------------
    | ENGINEER — REQUEST PAGE
    |--------------------------------------------------------------------------
    */
    public function engineerRequest()
    {
        $user = auth()->user();

        $permintaan = Permintaan::with('user')
            ->orderBy('tanggal_permintaan', 'desc')
            ->get();

        return view('engineer.request', compact('user', 'permintaan'));
    }

    /*
    |--------------------------------------------------------------------------
    | ENGINEER — MASTER SCHEDULE PAGE
    |--------------------------------------------------------------------------
    */
    public function engineerMaster()
    {
        $user = auth()->user();

        $schedules = Schedule::with(['mesin', 'partList'])
            ->orderBy('tanggal_plan', 'asc')
            ->get();

        return view('engineer.master', compact('user', 'schedules'));
    }
}