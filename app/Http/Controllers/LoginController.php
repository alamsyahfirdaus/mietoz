<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Method untuk menampilkan halaman login
    public function index()
    {
        return view('login');
    }

    public function authenticate(Request $request): JsonResponse
    {
        // Validasi input dari form login
        $credentials = $request->validate([
            'username' => ['required'], // Username harus diisi
            'password' => ['required'], // Password harus diisi
        ]);
    
        // Coba melakukan otentikasi pengguna dengan kredensial yang diberikan
        if (Auth::attempt($credentials)) {
            // Jika otentikasi berhasil, perbarui sesi
            $request->session()->regenerate();
    
            // Mendapatkan instance pengguna yang sedang login
            $user = Auth::user();
            
            // Menyimpan role dan level pengguna ke dalam sesi
            session([
                'role'  => !$user->hasCustomerId() ? 1 : 2, // Role 1 jika tidak memiliki Customer ID, 2 jika memiliki
                'level' => $user->level // Menyimpan level pengguna dari kolom 'level' di tabel 'users'
            ]);
    
            // Kirim respons sukses
            return response()->json(['success' => true]);
        }
    
        // Jika otentikasi gagal, kirim respons dengan kode status 422 dan pesan error
        return response()->json(['error' => 'Login Gagal!'], 422);
    }       

    public function logout(Request $request)
    {
        // Lakukan logout pengguna
        Auth::logout();

        // Invalidasi sesi
        $request->session()->invalidate();

        // Regenerate token sesi untuk mencegah serangan CSRF
        $request->session()->regenerateToken();

        // Redirect pengguna ke halaman login setelah logout
        return redirect('shop')->with([
            'logout' => true, // Tambahkan data sesi untuk menandai logout
        ])->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
