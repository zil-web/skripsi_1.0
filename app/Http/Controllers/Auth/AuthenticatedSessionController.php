<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        // Try web (users) guard first
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        // Then try kepsek guard
        if (Auth::guard('kepsek')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/kepsek/dashboard');
        }

        // Failed both
        // Record failed attempt for throttle
        \Illuminate\Support\Facades\RateLimiter::hit($request->throttleKey());

        return back()
            ->withErrors(['username' => trans('auth.failed')])
            ->onlyInput('username');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // logout both guards to ensure single logout endpoint
        Auth::guard('web')->logout();
        Auth::guard('kepsek')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah logout');
    }
}
