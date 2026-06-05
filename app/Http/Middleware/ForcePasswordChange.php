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

        if (Auth::check() && Auth::user()->force_password_change) {
            // Allow logout, change password, and notification routes
            if (!$request->routeIs('auth.change-password.*', 'logout', 'notifications.*')) {
                // Prevent redirect loop - check if we already redirected in this session
                if ($request->session()->get('_force_password_redirect_attempted', false)) {
                    // Already tried redirecting, let it through to avoid loop
                    return $next($request);
                }
                
                $request->session()->put('_force_password_redirect_attempted', true);
                return redirect()->route('auth.change-password.show');
            } else {
                // Clear the flag when on allowed routes
                $request->session()->forget('_force_password_redirect_attempted');
            }
        }

        return $next($request);
    }
}