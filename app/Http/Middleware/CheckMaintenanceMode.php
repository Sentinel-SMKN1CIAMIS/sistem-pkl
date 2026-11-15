<?php

namespace App\Http\Middleware;

use App\Services\MaintenanceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Rute dan path yang dikecualikan dari pemblokiran mode pemeliharaan.
     */
    protected array $exceptRoutes = [
        'maintenance',
        'login',
        'logout',
        'auth.change-password.show',
        'auth.change-password.update',
    ];

    protected array $exceptPaths = [
        'maintenance',
        'login',
        'logout',
        'up',
        'storage/*',
        'build/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jika mode maintenance tidak aktif, teruskan request tanpa hambatan
        if (!MaintenanceService::isMaintenanceActive()) {
            return $next($request);
        }

        // 2. Izinkan rute pengecualian (seperti halaman login, logout, dan halaman informasi maintenance)
        foreach ($this->exceptRoutes as $routeName) {
            if ($request->routeIs($routeName)) {
                return $next($request);
            }
        }

        foreach ($this->exceptPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        // 3. Periksa hak akses pengguna yang sedang login
        if (Auth::check()) {
            $user = Auth::user();

            // Jika peran pengguna diizinkan (misal Super Admin atau Pokja yang diceklis)
            if (MaintenanceService::isRoleAllowed($user->role)) {
                return $next($request);
            }

            // Jika peran pengguna TIDAK diizinkan saat maintenance aktif
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'maintenance',
                    'message' => MaintenanceService::getMessage(),
                ], 503);
            }

            return redirect()->route('maintenance')->with('warning', 'Peran akun Anda saat ini dibatasi karena mode pemeliharaan sedang aktif.');
        }

        // 4. Pengunjung umum (tamu / belum login) yang mengakses halaman selain login
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'maintenance',
                'message' => MaintenanceService::getMessage(),
            ], 503);
        }

        return redirect()->route('maintenance');
    }
}
