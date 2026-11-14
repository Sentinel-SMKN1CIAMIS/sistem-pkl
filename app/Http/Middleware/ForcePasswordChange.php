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

        if (Auth::check()) {
            $user = Auth::user();
            $allowedRoles = ['siswa', 'pembimbing_sekolah', 'pembimbing_dudi'];

            if (in_array($user->role, $allowedRoles)) {
                $needsChange = (bool) $user->force_password_change;

                // Auto-detect if user is still using default password
                if (!$needsChange) {
                    if ($user->role === 'siswa' && $user->siswa && !empty($user->siswa->nis)) {
                        if (\Illuminate\Support\Facades\Hash::check($user->siswa->nis, $user->password) || (!empty($user->siswa->nisn) && \Illuminate\Support\Facades\Hash::check($user->siswa->nisn, $user->password))) {
                            $needsChange = true;
                            $user->force_password_change = true;
                            $user->saveQuietly();
                        }
                    } elseif ($user->role === 'pembimbing_sekolah') {
                        $nip = $user->pembimbingSekolah?->nip;
                        if (($nip && \Illuminate\Support\Facades\Hash::check($nip, $user->password)) || \Illuminate\Support\Facades\Hash::check('smkn1ciamis', $user->password)) {
                            $needsChange = true;
                            $user->force_password_change = true;
                            $user->saveQuietly();
                        }
                    }
                }

                if ($needsChange) {
                    // Allow logout, change password, notifications, and student help center routes
                    if (!$request->routeIs('auth.change-password.*', 'logout', 'notifications.*', 'siswa.bantuan.*')) {
                        return redirect()->route('auth.change-password.show');
                    }
                }
            }
        }

        return $next($request);
    }
}