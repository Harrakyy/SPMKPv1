<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\PartList;
use Illuminate\Http\Request;

class PermintaanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Tampilkan semua permintaan
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $permintaan = Permintaan::with(['user', 'partLists'])
            ->orderBy('tanggal_permintaan', 'desc')
            ->paginate();

        return view('admin.request', compact('permintaan'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — Simpan permintaan baru
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_produk'         => 'required|string|max:100',
            'deskripsi_kebutuhan'  => 'required|string',
            'priority'             => 'required|in:low,medium,high,urgent',
            'tanggal_permintaan'   => 'required|date',
            'tanggal_selesai'      => 'nullable|date|after:tanggal_permintaan',
            'catatan'              => 'nullable|string|max:500',
        ], [
            'jenis_produk.required'        => 'Jenis produk wajib diisi.',
            'deskripsi_kebutuhan.required' => 'Deskripsi kebutuhan wajib diisi.',
            'priority.required'            => 'Priority wajib dipilih.',
            'tanggal_permintaan.required'  => 'Tanggal permintaan wajib diisi.',
            'tanggal_selesai.after'        => 'Tanggal selesai harus setelah tanggal permintaan.',
        ]);

        Permintaan::create([
            'user_id'             => auth()->id(),
            'nomor_permintaan'    => Permintaan::generateNomorPermintaan(),
            'jenis_produk'        => $request->jenis_produk,
            'deskripsi_kebutuhan' => $request->deskripsi_kebutuhan,
            'priority'            => $request->priority,
            'status'              => 'submitted',
            'tanggal_permintaan'  => $request->tanggal_permintaan,
            'tanggal_selesai'     => $request->tanggal_selesai,
            'catatan'             => $request->catatan,
        ]);

        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Permintaan berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Detail permintaan beserta part list
    |--------------------------------------------------------------------------
    */
    public function show(string $id)
    {
        $permintaan = Permintaan::with(['user', 'partLists'])
            ->findOrFail($id);

        return view('admin.request', compact('permintaan'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE — Setujui permintaan
    |--------------------------------------------------------------------------
    */
public function approve(Permintaan $permintaan)
{
    if (!in_array($permintaan->status, ['submitted', 'draft'])) {
        return back()->with('error', 'Permintaan ini tidak bisa disetujui.');
    }

    $permintaan->update(['status' => 'approved']);

    // Otomatis buat mesin terkait permintaan ini
    \App\Models\Mesin::firstOrCreate(
        ['permintaan_id' => $permintaan->permintaan_id],
        [
            'kode_mesin'   => 'M-' . $permintaan->nomor_permintaan,
            'nama_mesin'   => $permintaan->jenis_produk,
            'jenis_proses' => 'umum',
            'lokasi'       => '-',
            'status'       => 'active',
        ]
    );

    return back()->with('success', "Permintaan {$permintaan->nomor_permintaan} berhasil disetujui.");
}

    /*
    |--------------------------------------------------------------------------
    | REJECT — Tolak permintaan
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $permintaan->update([
            'status'  => 'rejected',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', "Permintaan {$permintaan->nomor_permintaan} berhasil ditolak.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — Hapus permintaan
    |--------------------------------------------------------------------------
    */
    public function destroy(string $id)
    {
        $permintaan = Permintaan::findOrFail($id);

        // Cegah hapus jika sudah in_progress atau completed
        if (in_array($permintaan->status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Permintaan yang sedang berjalan tidak bisa dihapus.');
        }

        $permintaan->delete();

        return back()->with('success', 'Permintaan berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS PART — Update status part dari permintaan
    |--------------------------------------------------------------------------
    */
    public function updatePartStatus(Request $request, string $partListId)
    {
        $request->validate([
            'status_part' => 'required|string',
        ]);

        $part = PartList::findOrFail($partListId);
        $part->update(['status_part' => $request->status_part]);

        return back()->with('success', 'Status part berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | Method yang tidak dipakai (wajib ada karena resource)
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return redirect()->route('admin.permintaan.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('admin.permintaan.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.permintaan.index');
    }
}