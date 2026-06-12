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
        // Store Kaprog's program_keahlian_id and allowed konsentrasi_keahlian_ids in request for easy access
        if (auth()->check() && auth()->user()->role === 'kaprog') {
            $user = auth()->user();
            $request->attributes->set('kaprog_program_id', $user->program_keahlian_id);
            
            $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();
            $request->attributes->set('kaprog_konsentrasi_ids', $allowedIds);
        }

        return $next($request);
    }
}
