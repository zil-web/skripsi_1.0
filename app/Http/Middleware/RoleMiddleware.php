<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (strtolower((string) auth()->user()->role) !== strtolower($role)) {
            abort(403);
        }

        return $next($request);
    }
}
