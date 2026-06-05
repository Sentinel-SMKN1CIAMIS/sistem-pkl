<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KaprogClassFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Store Kaprog's konsentrasi_keahlian_id in request for easy access
        if (auth()->check() && auth()->user()->role === 'kaprog') {
            $request->attributes->set('kaprog_konsentrasi_id', auth()->user()->konsentrasi_keahlian_id);
        }

        return $next($request);
    }
}
