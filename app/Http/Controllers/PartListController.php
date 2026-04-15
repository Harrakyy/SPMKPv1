<?php

namespace App\Http\Controllers;

use App\Models\PartList;
use App\Models\Permintaan;
use App\Models\Mesin;
use App\Models\User;
use Illuminate\Http\Request;

class PartListController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        // AJAX: load part list per permintaan (dari request.blade.php)
        if ($request->ajax() && $request->has('permintaan_id')) {
            $parts = PartList::where('permintaan_id', $request->permintaan_id)->get();
            return response()->json($parts);
        }

        $parts = PartList::with(['permintaan', 'designer'])
            ->orderBy('created_at', 'desc')
            ->get();

        $permintaan = Permintaan::with('partLists')
            ->orderBy('created_at', 'desc')
            ->get();

        $role = auth()->user()->role;

        if ($role === 'engineer') {
            return view('engineer.parts', compact('parts', 'permintaan'));
        }

        $mesins    = Mesin::orderBy('nama_mesin')->get();
        $designers = User::where('role', 'engineer')->orderBy('nama')->get();

        return view('admin.planning', compact('parts', 'permintaan', 'mesins', 'designers'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'permintaan_id'   => 'required|exists:permintaan,permintaan_id',
            'nama_part'       => 'required|string|max:150',
            'material'        => 'nullable|string|max:100',
            'dimensi'         => 'nullable|string|max:100',
            'dimensi_belanja' => 'nullable|string|max:100',
            'quantity'        => 'required|integer|min:1',
            'purchase'        => 'required|string',
            'berat'           => 'nullable|numeric|min:0',
            'status_part'     => 'required|in:draft,belum_dibeli,dibeli,indent,ready',
        ], [
            'permintaan_id.required' => 'Permintaan wajib dipilih.',
            'nama_part.required'     => 'Nama part wajib diisi.',
            'quantity.required'      => 'Quantity wajib diisi.',
            'unit.required'          => 'Unit wajib diisi.',
            'status_part.required'   => 'Status wajib dipilih.',
        ]);

        $data               = $request->all();
        $data['kode_part']  = PartList::generateKodePart($request->permintaan_id);

        PartList::create($data);

        $role = auth()->user()->role;

        if ($role === 'engineer') {
            return redirect()->route('engineer.parts.index')
                ->with('success', 'Part berhasil ditambahkan.');
        }

        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Part berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(PartList $partList)
    {
        $partList->load(['permintaan', 'designer', 'prosesMfg']);

        $role = auth()->user()->role;

        if ($role === 'engineer') {
            return view('engineer.parts-detail', compact('partList'));
        }

        return redirect()->route('admin.permintaan.index');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT — kembalikan JSON untuk modal
    |--------------------------------------------------------------------------
    */
    public function edit(PartList $partList)
    {
        if (request()->ajax()) {
            return response()->json($partList);
        }

        return redirect()->route('admin.permintaan.index');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, PartList $partList)
    {
        $request->validate([
            'nama_part'       => 'required|string|max:150',
            'material'        => 'nullable|string|max:100',
            'dimensi'         => 'nullable|string|max:100',
            'dimensi_belanja' => 'nullable|string|max:100',
            'quantity'        => 'required|integer|min:1',
            'purchase'            => 'required|string|max:20',
            'berat'           => 'nullable|numeric|min:0',
            'status_part'     => 'required|in:draft,belum_dibeli,dibeli,indent,ready',
            'designer_id'     => 'nullable|exists:users,user_id',
        ]);

        $partList->update($request->all());

        $role = auth()->user()->role;

        if ($role === 'engineer') {
            return redirect()->route('engineer.parts.index')
                ->with('success', 'Part berhasil diperbarui.');
        }

        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Part berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(PartList $partList)
    {
        // Support AJAX delete (dari request.blade.php)
        if (request()->ajax() || request()->wantsJson()) {
            $partList->delete();
            return response()->json(['success' => true]);
        }

        $partList->delete();

        $role = auth()->user()->role;

        if ($role === 'engineer') {
            return redirect()->route('engineer.parts.index')
                ->with('success', 'Part berhasil dihapus.');
        }

        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Part berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN DESIGNER
    |--------------------------------------------------------------------------
    */
    public function assignDesigner(Request $request, PartList $partList)
    {
        $request->validate([
            'designer_id' => 'required|exists:users,user_id',
        ]);

        $partList->update([
            'designer_id' => $request->designer_id,
            'status_part' => 'draft',
        ]);

        return back()->with('success', 'Designer berhasil ditugaskan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */
    public function updatePartStatus(Request $request, PartList $partList)
    {
        $request->validate([
            'status_part' => 'required|in:draft,belum_dibeli,dibeli,indent,ready',
        ]);

        $partList->update(['status_part' => $request->status_part]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Status part berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE (redirect only)
    |--------------------------------------------------------------------------
    */
    public function create($permintaan_id = null)
    {
        return redirect()->route('admin.permintaan.index');
    }
}