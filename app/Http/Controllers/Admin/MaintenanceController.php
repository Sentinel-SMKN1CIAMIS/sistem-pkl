<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan mode maintenance (khusus Super Admin).
     */
    public function index()
    {
        $summary = MaintenanceService::getSummary();
        $allRoles = MaintenanceService::ALL_ROLES;

        return view('admin.maintenance.index', compact('summary', 'allRoles'));
    }

    /**
     * Perbarui pengaturan mode maintenance.
     */
    public function update(Request $request)
    {
        $request->validate([
            'is_active' => 'required|in:0,1',
            'allowed_roles' => 'nullable|array',
            'allowed_roles.*' => 'string|in:' . implode(',', array_keys(MaintenanceService::ALL_ROLES)),
            'message' => 'nullable|string|max:1000',
        ]);

        $isActive = $request->input('is_active') === '1';
        $allowedRoles = $request->input('allowed_roles', ['super_admin']);
        $message = $request->input('message');

        MaintenanceService::updateSettings(
            active: $isActive,
            allowedRoles: $allowedRoles,
            message: $message,
            byUser: Auth::user()
        );

        // Catat aktivitas di ActivityLog jika tersedia
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => $isActive ? 'Mengaktifkan Mode Pemeliharaan' : 'Menonaktifkan Mode Pemeliharaan',
                'description' => $isActive
                    ? 'Mode maintenance diaktifkan. Peran diizinkan: ' . implode(', ', $allowedRoles)
                    : 'Mode maintenance dinonaktifkan. Sistem dibuka kembali untuk seluruh pengguna.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Silently skip if log table not ready
        }

        $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Mode pemeliharaan berhasil {$statusText}. Pengaturan hak akses telah diterapkan.");
    }

    /**
     * Tampilkan halaman publik peringatan mode maintenance.
     */
    public function showMaintenancePage()
    {
        if (!MaintenanceService::isMaintenanceActive()) {
            if (Auth::check()) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('login');
        }

        $summary = MaintenanceService::getSummary();
        $allRoles = MaintenanceService::ALL_ROLES;

        return response()->view('errors.maintenance', compact('summary', 'allRoles'), 503);
    }
}
