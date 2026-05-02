<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthKepsek
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated using kepsek guard
        if (!Auth::guard('kepsek')->check()) {
            return redirect()->route('login')->withErrors(['message' => 'Silakan login sebagai Kepala Sekolah']);
        }

        // Check if kepala sekolah is active
        $kepsek = Auth::guard('kepsek')->user();
        if ($kepsek && method_exists($kepsek, 'isActive') && !$kepsek->isActive()) {
            Auth::guard('kepsek')->logout();
            return redirect()->route('login')->withErrors([
                'message' => 'Akun Anda tidak aktif.',
            ]);
        }

        return $next($request);
    }
}
