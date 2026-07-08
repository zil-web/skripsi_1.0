<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')->withErrors(['message' => 'Silakan login sebagai Admin']);
        }

        $user = Auth::guard('web')->user();
        if (!$user || strtolower((string) ($user->role ?? '')) !== 'admin') {
            Auth::guard('web')->logout();

            return redirect()->route('login')->withErrors([
                'message' => 'Akses ditolak. Akun ini bukan admin.',
            ]);
        }

        return $next($request);
    }
}
