<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Mesin;
use App\Models\PartList;
use App\Models\ProsesMfg;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Tampilkan semua schedule
    |--------------------------------------------------------------------------
    | View berbeda tergantung role:
    | - Admin   → machining/schedulemfg/index.blade.php
    | - Operator → machining/schedulemfg/index.blade.php (sama, filter by PIC)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = auth()->user();

        $query = Schedule::with(['mesin', 'partList', 'picUser'])
            ->orderBy('tanggal_plan', 'asc');

        // Operator hanya lihat schedule miliknya
        if ($user->role === 'operator') {
            $query->where('pic', $user->user_id);
        }

        $schedules = $query->get();

        return view('operator.schedulemfg.index', compact('schedules'));
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER INDEX — Tampilkan halaman master data (operator)
    |--------------------------------------------------------------------------
    | Route: GET /operator/master
    | Route name: operator.master
    |--------------------------------------------------------------------------
    */
    public function masterIndex()
    {
        $user = auth()->user();

        $schedules = Schedule::with(['mesin', 'partList', 'picUser'])
            ->where('pic', $user->user_id)
            ->orderBy('tanggal_plan', 'asc')
            ->get();

        $mesins     = Mesin::where('status', 'active')->get();
        $partLists  = PartList::all();
        $operators  = User::where('role', 'operator')->get();
        $proses_mfg = ProsesMfg::all();

        return view('operator.master', compact(
            'schedules',
            'mesins',
            'partLists',
            'operators',
            'proses_mfg'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE — Form buat schedule baru
    |--------------------------------------------------------------------------
    */
    public function create($partlist_id = null)
    {
        $part      = $partlist_id ? PartList::find($partlist_id) : null;
        $mesins    = Mesin::where('status', 'active')->get();

        $machinings = User::where('role', 'operator')->get();

        $proses_mfg = $partlist_id
            ? ProsesMfg::where('partlist_id', $partlist_id)->get()
            : collect();

        return view('operator.schedulemfg.index', compact('part', 'mesins', 'machinings', 'proses_mfg'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — Simpan schedule baru
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'mesin_id'             => 'required|exists:mesin,mesin_id',
            'partlist_id'          => 'required|exists:part_list,partlist_id',
            'mfg_id'               => 'required|exists:proses_mfg,mfg_id',
            'activity'             => 'required|string',
            'pic'                  => 'required|exists:users,user_id',
            'tanggal_plan'         => 'required|date',
            'start_time_plan'      => 'nullable|date_format:H:i',
            'end_time_plan'        => 'nullable|date_format:H:i|after:start_time_plan',
            'durasi_plan_minutes'  => 'nullable|integer|min:1',
        ], [
            'mesin_id.required'    => 'Mesin wajib dipilih.',
            'mesin_id.exists'      => 'Mesin tidak ditemukan.',
            'partlist_id.required' => 'Part List wajib dipilih.',
            'pic.required'         => 'PIC wajib dipilih.',
            'tanggal_plan.required'=> 'Tanggal plan wajib diisi.',
            'tanggal_plan.date'    => 'Format tanggal tidak valid.',
            'activity.required'    => 'Activity wajib diisi.',
        ]);

        $schedule = Schedule::create($request->all());

        // Update status ProsesMfg menjadi pending
        $prosesMfg = ProsesMfg::find($request->mfg_id);
        if ($prosesMfg) {
            $prosesMfg->update(['status' => 'pending']);
        }

        $redirectRoute = auth()->user()->role === 'admin'
            ? 'admin.schedule.index'
            : 'operator.schedule.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Schedule berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | START SCHEDULE — Mulai pengerjaan
    |--------------------------------------------------------------------------
    */
    public function startSchedule(Schedule $schedule)
    {
        // Cek apakah user adalah PIC schedule ini
        if (auth()->user()->user_id !== $schedule->pic) {
            return back()->with('error', 'Anda bukan PIC untuk schedule ini.');
        }

        // Cek apakah schedule sudah dimulai
        if ($schedule->status === 'in_progress') {
            return back()->with('error', 'Schedule ini sudah berjalan.');
        }

        $schedule->update([
            'status'           => 'in_progress',
            'machining_status' => 'in_progress',
            'tanggal_act'      => now()->toDateString(),
            'start_time_act'   => now()->toTimeString(),
        ]);

        // Update ProsesMfg
        $prosesMfg = ProsesMfg::find($schedule->mfg_id);
        if ($prosesMfg) {
            $prosesMfg->update([
                'status'      => 'running',
                'start_time'  => now(),
                'operator_id' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Schedule berhasil dimulai.');
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE SCHEDULE — Selesaikan pengerjaan
    |--------------------------------------------------------------------------
    */
    public function completeSchedule(Request $request, Schedule $schedule)
    {
        $request->validate([
            'durasi_act_minutes' => 'required|integer|min:1',
            'catatan'            => 'nullable|string|max:500',
            'hasil_ok'           => 'nullable|integer|min:0',
            'hasil_ng'           => 'nullable|integer|min:0',
        ], [
            'durasi_act_minutes.required' => 'Durasi aktual wajib diisi.',
            'durasi_act_minutes.integer'  => 'Durasi harus berupa angka.',
            'durasi_act_minutes.min'      => 'Durasi minimal 1 menit.',
        ]);

        // Cek apakah schedule sudah selesai
        if ($schedule->status === 'completed') {
            return back()->with('error', 'Schedule ini sudah selesai.');
        }

        $schedule->update([
            'status'              => 'completed',
            'machining_status'    => 'completed',
            'end_time_act'        => now()->toTimeString(),
            'durasi_act_minutes'  => $request->durasi_act_minutes,
            'catatan'             => $request->catatan,
        ]);

        // Update ProsesMfg
        $prosesMfg = ProsesMfg::find($schedule->mfg_id);
        if ($prosesMfg) {
            $prosesMfg->update([
                'status'    => 'completed',
                'end_time'  => now(),
                'hasil_ok'  => $request->hasil_ok ?? 0,
                'hasil_ng'  => $request->hasil_ng ?? 0,
            ]);
        }

        return back()->with('success', 'Schedule berhasil diselesaikan.');
    }
    public function activityDetail($status)
{
    if ($status == 'act') {
        $status = 'in_progress';
    } elseif ($status == 'done') {
        $status = 'completed';
    }

    $data = Schedule::where('status', $status)
        ->with(['mesin', 'partList'])
        ->get();

    return view('operator.schedulemfg.activity-details', compact('data'));
}

}