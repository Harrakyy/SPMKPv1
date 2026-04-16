<?php
//MesinController.php
namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\Permintaan;
use Illuminate\Http\Request;

class MesinController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.planning.index');
    }

    public function create()
    {
        return redirect()->route('admin.planning.index');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — Simpan mesin baru, bisa terkait permintaan tertentu
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'kode_mesin'    => 'required|unique:mesin,kode_mesin',
            'nama_mesin'    => 'required|string|max:100',
            'jenis_proses'  => 'required|string|max:100',
            'lokasi'        => 'required|string|max:100',
            'permintaan_id' => 'nullable|exists:permintaan,permintaan_id',
        ], [
            'kode_mesin.unique' => 'Kode mesin sudah digunakan.',
            'kode_mesin.required' => 'Kode mesin wajib diisi.',
            'nama_mesin.required' => 'Nama mesin wajib diisi.',
            'jenis_proses.required' => 'Jenis proses wajib dipilih.',
            'lokasi.required' => 'Lokasi wajib diisi.',
        ]);

        Mesin::create([
            'kode_mesin'    => $request->kode_mesin,
            'nama_mesin'    => $request->nama_mesin,
            'jenis_proses'  => $request->jenis_proses,
            'lokasi'        => $request->lokasi,
            'status'        => 'active',
            'permintaan_id' => $request->permintaan_id ?? null,
        ]);

        return redirect()->route('admin.planning.index')
            ->with('success', 'Mesin berhasil ditambahkan.');
    }

    public function edit(Mesin $mesin)
    {
        if (request()->ajax()) {
            return response()->json($mesin);
        }
        return redirect()->route('admin.planning.index');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — Perbarui data mesin
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Mesin $mesin)
    {
        $request->validate([
            'kode_mesin'    => 'required|unique:mesin,kode_mesin,' . $mesin->mesin_id . ',mesin_id',
            'nama_mesin'    => 'required|string|max:100',
            'jenis_proses'  => 'required|string|max:100',
            'status'        => 'required|in:active,maintenance,inactive',
            'kapasitas'     => 'nullable|numeric|min:0',
            'lokasi'        => 'required|string|max:100',
            'permintaan_id' => 'nullable|exists:permintaan,permintaan_id',
        ]);

        $mesin->update($request->all());

        return redirect()->route('admin.planning.index')
            ->with('success', 'Mesin berhasil diperbarui.');
    }

    public function destroy(Mesin $mesin)
    {
        $mesin->delete();

        return redirect()->route('admin.planning.index')
            ->with('success', 'Mesin berhasil dihapus.');
    }

    public function updateStatus(Request $request, Mesin $mesin)
    {
        $request->validate([
            'status'           => 'required|in:active,maintenance,inactive',
            'last_maintenance' => 'nullable|date',
        ]);

        $mesin->update([
            'status'           => $request->status,
            'last_maintenance' => $request->last_maintenance ?? $mesin->last_maintenance,
        ]);

        return back()->with('success', 'Status mesin berhasil diperbarui.');
    }
}