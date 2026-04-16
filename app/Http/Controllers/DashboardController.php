<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        $stats = [];

        switch ($role) {
            case 'siswa':
                $siswa = auth()->user()->siswa;
                $stats = [
                    'jurnal_total' => \App\Models\Jurnal::where('siswa_id', $siswa->id)->count(),
                    'jurnal_valid' => \App\Models\Jurnal::where('siswa_id', $siswa->id)->where('status', 'valid')->count(),
                    'absensi_count' => \App\Models\Absensi::where('siswa_id', $siswa->id)->count(),
                ];
                return view('dashboards.siswa', compact('stats'));
            case 'pembimbing_sekolah':
                $teacher = auth()->user()->pembimbingSekolah;
                $stats = [
                    'siswa_count' => \App\Models\Siswa::where('pembimbing_sekolah_id', $teacher->id)->count(),
                    'jurnal_pending' => \App\Models\Jurnal::whereHas('siswa', function($q) use ($teacher) {
                        $q->where('pembimbing_sekolah_id', $teacher->id);
                    })->where('status', 'pending')->count(),
                ];
                return view('dashboards.pembimbing-sekolah', compact('stats'));
            case 'pembimbing_dudi':
                $mentor = auth()->user()->pembimbingDudi;
                $stats = [
                    'siswa_count' => \App\Models\Siswa::where('dudi_id', $mentor->dudi_id)->count(),
                    'jurnal_pending' => \App\Models\Jurnal::whereHas('siswa', function($q) use ($mentor) {
                        $q->where('dudi_id', $mentor->dudi_id);
                    })->where('status', 'pending')->count(),
                ];
                return view('dashboards.pembimbing-dudi', compact('stats'));
            case 'pokja':
            case 'super_admin':
                $stats = [
                    'total_siswa' => \App\Models\Siswa::count(),
                    'total_dudi' => \App\Models\Dudi::count(),
                    'total_pembimbing' => \App\Models\PembimbingSekolah::count(),
                ];
                return view('dashboards.' . str_replace('_', '-', $role), compact('stats'));
            default:
                abort(403, 'Unauthorized action.');
        }
    }
}
