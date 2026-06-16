<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Check if route requires 'pembimbing_sekolah' and user has a pembimbingSekolah profile
        if (in_array('pembimbing_sekolah', $roles) && $user->pembimbingSekolah()->exists()) {
            return $next($request);
        }

        // Check if route requires 'pembimbing_dudi' and user has a pembimbingDudi profile
        if (in_array('pembimbing_dudi', $roles) && $user->pembimbingDudi()->exists()) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
