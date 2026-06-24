<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PokjaGroupMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures user is either super_admin or part of an active Pokja group
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Skip check if user must change password - they should complete that first
        if ($user->force_password_change) {
            return $next($request);
        }

        // Super admin and Kepala Sekolah have full access bypass
        if ($user->role === 'super_admin' || $user->role === 'kepala_sekolah') {
            return $next($request);
        }

        // Pokja users must be part of an active group
        if ($user->role === 'pokja') {
            if (!$user->hasActivePokjaGroup()) {
                abort(403, 'Anda belum menjadi bagian dari grup Pokja manapun.');
            }
        }

        return $next($request);
    }
}
