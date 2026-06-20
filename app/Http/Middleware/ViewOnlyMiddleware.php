<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'kepala_sekolah') {
            if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
                if ($request->routeIs('logout')) {
                    return $next($request);
                }
                abort(403, 'Aksi ini tidak diizinkan untuk akun Kepala Sekolah (View Only).');
            }
        }

        return $next($request);
    }
}
