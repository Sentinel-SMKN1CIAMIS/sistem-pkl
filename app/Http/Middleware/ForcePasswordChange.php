<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for AJAX/JSON requests (notifikasi, polling, etc)
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return $next($request);
        }

        // Skip middleware for asset/debug routes
        if ($request->is('api/*') || $request->is('_debugbar/*') || $request->is('livewire/*')) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->force_password_change) {
            // Allow logout, change password, and notification routes
            if (!$request->routeIs('auth.change-password.*', 'logout', 'notifications.*')) {
                return redirect()->route('auth.change-password.show');
            }
        }

        return $next($request);
    }
}