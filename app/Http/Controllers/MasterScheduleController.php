<?php
//MasterScheduleController
namespace App\Http\Controllers;

use App\Models\ProsesMfg;
use Illuminate\Http\Request;

class MasterScheduleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Tampilkan semua activity dari semua mesin
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = ProsesMfg::with([
            'mesin',
            'mesin.permintaan',
            'partList',
        ])->orderBy('tanggal_planning', 'asc');

        // Filter by status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->get();

        $stats = [
            'total' => $activities->count(),
            'plan'  => $activities->where('status', 'pending')->count(),
            'act'   => $activities->where('status', 'running')->count(),
            'done'  => $activities->where('status', 'completed')->count(),
        ];

        return view('admin.master', compact('activities', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — Admin buat activity dari master schedule
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'mesin_id'      => 'required|exists:mesin,mesin_id',
            'partlist_id'   => 'nullable|exists:part_list,partlist_id',
            'nama_activity' => 'required|string|max:255',
            'pic'           => 'required|string|max:100',
            'tanggal_actual'  => 'required|date',
            'status'        => 'required|in:pending,running,completed',
        ], [
            'mesin_id.required'      => 'Mesin wajib dipilih.',
            'nama_activity.required' => 'Nama activity wajib diisi.',
            'pic.required'           => 'PIC wajib diisi.',
            'tanggal_actual.required'  => 'Tanggal actual wajib diisi.',
        ]);

        ProsesMfg::create([
            'mesin_id'     => $request->mesin_id,
            'partlist_id'  => $request->partlist_id ?? null,
            'proses_nama'  => $request->nama_activity,
            'pic'          => $request->pic,
            'tanggal_actual' => $request->tanggal_actual,
            'status'       => $request->status,
        ]);

        return redirect()->route('admin.master.index')
            ->with('success', 'Activity berhasil ditambahkan ke Master Schedule.');
    }

public function updateStatus(Request $request, $id)
{
    $proses = ProsesMfg::where('mfg_id', $id)->firstOrFail();
    $proses->update([
        'status'         => $request->status,
        'tanggal_actual' => $request->status === 'running' ? now() : $proses->tanggal_actual,
    ]);
    return back()->with('success', 'Status berhasil diperbarui.');
}

public function update(Request $request, $id)
{
    $proses = ProsesMfg::where('mfg_id', $id)->firstOrFail();
    $request->validate([
        'nama_activity'    => 'required|string|max:255',
        'pic'              => 'required|string|max:100',
        'tanggal_planning' => 'required|date',
    ]);
    $proses->update([
        'proses_nama'      => $request->nama_activity,
        'pic'              => $request->pic,
        'tanggal_planning' => $request->tanggal_planning,
    ]);
    return back()->with('success', 'Activity berhasil diperbarui.');
}
public function destroy($id)
{
    ProsesMfg::where('mfg_id', $id)->firstOrFail()->delete();
    return back()->with('success', 'Activity berhasil dihapus.');
}

}