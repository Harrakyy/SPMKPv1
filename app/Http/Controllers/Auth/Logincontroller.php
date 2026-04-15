<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN HALAMAN LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        // Jika sudah login, langsung redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | PROSES LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $email    = strtolower(trim($request->email));
        $password = $request->password;

        /*
        |--------------------------------------------------------------------------
        | CEK DOMAIN EMAIL PERUSAHAAN
        |--------------------------------------------------------------------------
        */

        if (!str_ends_with($email, '@sumberprimamandiri.com')) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Gunakan email perusahaan ');
        }

        /*
        |--------------------------------------------------------------------------
        | CEK USER DI DATABASE
        |--------------------------------------------------------------------------
        */

        $user = User::where('email', $email)->first();

        // Jika user tidak ditemukan
        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email tidak terdaftar. Hubungi admin.');
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFIKASI PASSWORD
        |--------------------------------------------------------------------------
        */

        if (!Hash::check($password, $user->password_hash)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau password salah.');
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN USER
        |--------------------------------------------------------------------------
        */

        Auth::login($user, $request->boolean('remember'));

        // Update waktu login terakhir
        $user->updateLastLogin();

        // Regenerasi session untuk keamanan
        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */

        return $this->redirectByRole($user);
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }


    /*
    |--------------------------------------------------------------------------
    | HELPER : REDIRECT BERDASARKAN ROLE
    |--------------------------------------------------------------------------
    */

    private function redirectByRole(User $user)
    {
        return match($user->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'engineer' => redirect()->route('engineer.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            default    => redirect()->route('login')->with('error', 'Role tidak dikenali.')
        };
    }
}