<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,engineer,operator,supervisor',
        ]);

        User::create([
            'nama'          => $request->nama,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'role'          => $request->role,
            'jabatan'       => $request->jabatan,
            'no_hp'         => $request->no_hp,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'role'  => 'required|in:admin,engineer,operator,supervisor',
        ]);

        $data = [
            'nama'    => $request->nama,
            'email'   => $request->email,
            'role'    => $request->role,
            'jabatan' => $request->jabatan,
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah admin hapus dirinya sendiri
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
    public function updateProfile(Request $request)
{
    $user = auth()->user();

    $request->validate([
        'alamat' => 'required|string|max:255',
        'no_hp'  => 'required|string|max:20',
        'password' => 'nullable|min:6',
    ]);

    $data = [
        'alamat' => $request->alamat,
        'no_hp'  => $request->no_hp,
    ];

    // Update password kalau diisi
    if ($request->filled('password')) {
        $data['password_hash'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('profile')
        ->with('success', 'Profil berhasil diperbarui!');
}
}