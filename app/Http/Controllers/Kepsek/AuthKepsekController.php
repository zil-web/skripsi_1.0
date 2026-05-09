php artisan migrate
php artisan db:seed --class=UserSeeder<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthKepsekController extends Controller
{
    /**
     * Show the login form for Kepala Sekolah
     */
    public function showLogin()
    {
        // Redirect to dashboard if already authenticated
        if (Auth::guard('kepsek')->check()) {
            return redirect()->route('kepsek.dashboard');
        }

        return view('kepsek.auth.login');
    }

    /**
     * Handle login request for Kepala Sekolah
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Attempt authentication using kepsek guard
        if (Auth::guard('kepsek')->attempt($credentials)) {
            // Get authenticated user
            $kepsek = Auth::guard('kepsek')->user();

            // Check if kepala sekolah is active
            if (!$kepsek->isActive()) {
                Auth::guard('kepsek')->logout();
                return back()->withErrors([
                    'username' => 'Akun Kepala Sekolah Anda tidak aktif. Silakan hubungi admin.',
                ]);
            }

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Log the successful login
            \Log::info('Kepala Sekolah login successful', [
                'id' => $kepsek->id,
                'nama' => $kepsek->nama,
                'username' => $kepsek->username,
            ]);

            return redirect()->intended(route('kepsek.dashboard'));
        }

        // Log failed login attempt
        \Log::warning('Kepala Sekolah login failed', [
            'username' => $credentials['username'] ?? 'unknown',
        ]);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout request for Kepala Sekolah
     */
    public function logout(Request $request)
    {
        // Get user before logout for logging
        $kepsek = Auth::guard('kepsek')->user();

        if ($kepsek) {
            \Log::info('Kepala Sekolah logout', [
                'id' => $kepsek->id,
                'nama' => $kepsek->nama,
            ]);
        }

        // Logout from kepsek guard
        Auth::guard('kepsek')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate session token
        $request->session()->regenerateToken();

        return redirect()->route('kepsek.login');
    }
}
