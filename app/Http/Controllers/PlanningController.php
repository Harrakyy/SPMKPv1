<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\PartList;
use App\Models\Permintaan;
use App\Models\ProsesMfg;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Daftar semua mesin beserta statistik activity
    |--------------------------------------------------------------------------
    */
public function index()
{
    $mesins = Mesin::with('permintaan')
        ->whereHas('permintaan', fn($q) => $q->where('status', 'approved'))
        ->withCount([
            'prosesMfg as total_activity',
            'prosesMfg as plan_count'  => fn($q) => $q->where('status', 'pending'),
            'prosesMfg as act_count'   => fn($q) => $q->where('status', 'running'),
            'prosesMfg as done_count'  => fn($q) => $q->where('status', 'completed'),
        ])->get();

    return view('admin.planning', compact('mesins'));
}

    /*
    |--------------------------------------------------------------------------
    | SHOW — Detail mesin + daftar activity + part list dari permintaan terkait
    |--------------------------------------------------------------------------
    */
    public function show($id)
{
    $mesin = Mesin::with('permintaan')->findOrFail($id);

    $proses = ProsesMfg::where('mesin_id', $id)
        ->with('partList')
        ->orderBy('tanggal_plan', 'asc')
        ->get();

    // Ambil part list dari permintaan yang terkait mesin ini
    $partLists = collect();
    if ($mesin->permintaan_id) {
        $partLists = PartList::where('permintaan_id', $mesin->permintaan_id)
            ->orderBy('nama_part')
            ->get();
    }

    return view('admin.planningshow', compact('mesin', 'proses', 'partLists'));
}
    /*
    |--------------------------------------------------------------------------
    | STORE — Simpan activity baru untuk mesin tertentu
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'mesin_id'      => 'required|exists:mesin,mesin_id',
            'nama_activity' => 'required|string|max:255',
            'pic'           => 'required|string|max:100',
            'tanggal_plan'  => 'required|date',
            'request_id'    => 'nullable|string|max:50',
            'partlist_id'   => 'nullable|exists:part_list,partlist_id',
        ], [
            'mesin_id.required'      => 'Mesin wajib dipilih.',
            'nama_activity.required' => 'Nama activity wajib diisi.',
            'pic.required'           => 'PIC wajib diisi.',
            'tanggal_plan.required'  => 'Tanggal plan wajib diisi.',
        ]);

        ProsesMfg::create([
            'mesin_id'     => $request->mesin_id,
            'partlist_id'  => $request->partlist_id ?? null,
            'proses_nama'  => $request->nama_activity,
            'pic'          => $request->pic,
            'tanggal_plan' => $request->tanggal_plan,
            'request_id'   => $request->request_id ?? null,
            'status'       => 'pending',
        ]);

        return redirect()->route('admin.planning.show', $request->mesin_id)
            ->with('success', 'Activity berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — Perbarui data activity
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $activity = ProsesMfg::findOrFail($id);

        $request->validate([
            'nama_activity' => 'required|string|max:255',
            'pic'           => 'required|string|max:100',
            'tanggal_plan'  => 'required|date',
            'partlist_id'   => 'nullable|exists:part_list,partlist_id',
        ], [
            'nama_activity.required' => 'Nama activity wajib diisi.',
            'pic.required'           => 'PIC wajib diisi.',
            'tanggal_plan.required'  => 'Tanggal plan wajib diisi.',
        ]);

        $activity->update([
            'proses_nama'  => $request->nama_activity,
            'pic'          => $request->pic,
            'tanggal_plan' => $request->tanggal_plan,
            'partlist_id'  => $request->partlist_id ?? $activity->partlist_id,
        ]);

        return redirect()->route('admin.planning.show', $activity->mesin_id)
            ->with('success', 'Activity berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — Hapus activity
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $activity = ProsesMfg::findOrFail($id);
        $mesinId  = $activity->mesin_id;
        $activity->delete();

        return redirect()->route('admin.planning.show', $mesinId)
            ->with('success', 'Activity berhasil dihapus.');
    }
}