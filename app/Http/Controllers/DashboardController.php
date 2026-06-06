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
                if (!$siswa->dudi_id) {
                    return redirect()->route('siswa.pengajuan_pkl.create');
                }
                $stats = [
                    'jurnal_total' => \App\Models\Jurnal::where('siswa_id', $siswa->id)->count(),
                    'jurnal_valid' => \App\Models\Jurnal::where('siswa_id', $siswa->id)->where('status', 'valid')->count(),
                    'absensi_count' => \App\Models\Absensi::where('siswa_id', $siswa->id)->count(),
                ];
                $forcePasswordChange = auth()->user()->force_password_change;
                return view('dashboards.siswa', compact('stats', 'forcePasswordChange'));
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
            case 'kaprog':
                // For demo, we show all data, or we could filter by kaprog's jurusan
                $activeStudentsCount = \App\Models\Siswa::where('status_pkl', 'sedang_pkl')->count();
                
                $today = \Carbon\Carbon::today();
                
                // Absensi stats
                $absensiTodayCount = \App\Models\Absensi::whereDate('tanggal', $today)->distinct('siswa_id')->count();
                $attendanceRate = $activeStudentsCount > 0 ? round(($absensiTodayCount / $activeStudentsCount) * 100) : 0;
                
                $siswaAbsenIds = \App\Models\Absensi::whereDate('tanggal', $today)->pluck('siswa_id')->toArray();
                $missingAttendance = \App\Models\Siswa::where('status_pkl', 'sedang_pkl')
                    ->whereNotIn('id', $siswaAbsenIds)
                    ->take(5)
                    ->get();

                // Jurnal stats
                $jurnalTodayCount = \App\Models\Jurnal::whereDate('tanggal', $today)->distinct('siswa_id')->count();
                $journalRate = $activeStudentsCount > 0 ? round(($jurnalTodayCount / $activeStudentsCount) * 100) : 0;
                
                $siswaJurnalIds = \App\Models\Jurnal::whereDate('tanggal', $today)->pluck('siswa_id')->toArray();
                $missingJournal = \App\Models\Siswa::where('status_pkl', 'sedang_pkl')
                    ->whereNotIn('id', $siswaJurnalIds)
                    ->take(5)
                    ->get();

                $stats = [
                    'total_siswa' => \App\Models\Siswa::count(),
                    'total_dudi' => \App\Models\Dudi::count(),
                    'total_pembimbing' => \App\Models\PembimbingSekolah::count(),
                    'pengajuan_menunggu' => \App\Models\PengajuanPkl::where('status', 'menunggu')->count(),
                    'attendance_rate' => $attendanceRate,
                    'journal_rate' => $journalRate,
                    'missing_attendance' => $missingAttendance,
                    'missing_journal' => $missingJournal,
                ];
                return view('dashboards.kaprog', compact('stats'));
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

    public function bulkAcc(Request $request)
    {
        $role = auth()->user()->role;
        if ($role === 'pembimbing_sekolah') {
            $teacher = auth()->user()->pembimbingSekolah;
            
            // ACC all pending jurnal
            \App\Models\Jurnal::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
            })->where('status', 'pending')->update(['status' => 'valid']);
            
            // ACC all pending absensi
            \App\Models\Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
            })->where('status', 'pending')->update(['status' => 'hadir']);
            
            return response()->json(['message' => 'Semua Jurnal dan Absensi berhasil di-ACC (Rapid Testing Mode)']);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
