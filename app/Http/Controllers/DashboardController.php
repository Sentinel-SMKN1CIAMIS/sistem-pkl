<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user->role;
        $stats = [];

        switch ($role) {
            case 'siswa':
                $siswa = $user->siswa;
                if (!$siswa->dudi_id) {
                    return redirect()->route('siswa.pengajuan_pkl.create');
                }
                $stats = \Illuminate\Support\Facades\Cache::remember("dashboard_siswa_{$siswa->id}", 300, function() use ($siswa) {
                    $startOfWeek = \Carbon\Carbon::now()->startOfWeek(1);
                    $endOfWeek = $startOfWeek->copy()->addDays(4);

                    $absensiWeek = \App\Models\Absensi::where(['siswa_id' => $siswa->id])
                        ->whereBetween('tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                        ->get()
                        ->keyBy(fn($r) => $r->tanggal);

                    $weekAttendanceData = [];
                    for ($i = 0; $i < 5; $i++) {
                        $dateStr = $startOfWeek->copy()->addDays($i)->toDateString();
                        $record = $absensiWeek->get($dateStr);
                        if ($record && $record->status === 'hadir' && $record->waktu_datang) {
                            $parts = explode(':', $record->waktu_datang);
                            $hour = (int)$parts[0];
                            $minutes = isset($parts[1]) ? (int)$parts[1] : 0;
                            $decimalTime = round($hour + ($minutes / 60), 2);
                            $weekAttendanceData[] = $decimalTime;
                        } else {
                            $weekAttendanceData[] = null;
                        }
                    }

                    return [
                        'jurnal_total' => \App\Models\Jurnal::where(['siswa_id' => $siswa->id])->count(),
                        'jurnal_valid' => \App\Models\Jurnal::where(['siswa_id' => $siswa->id, 'status' => 'valid'])->count(),
                        'absensi_count' => \App\Models\Absensi::where(['siswa_id' => $siswa->id])->count(),
                        'week_attendance' => $weekAttendanceData,
                    ];
                });
                $forcePasswordChange = $user->force_password_change;
                return view('dashboards.siswa', compact('stats', 'forcePasswordChange'));
            case 'pembimbing_sekolah':
                $teacher = $user->pembimbingSekolah;
                $stats = \Illuminate\Support\Facades\Cache::remember("dashboard_pembimbing_sekolah_{$teacher->id}", 300, function() use ($teacher) {
                    $jurnalMasuk = \App\Models\Jurnal::whereHas('siswa', function($q) use ($teacher) {
                        $q->where(['pembimbing_sekolah_id' => $teacher->id]);
                    })->count();

                    $weeksEvaluatedData = [];
                    for ($w = 3; $w >= 0; $w--) {
                        $start = \Carbon\Carbon::now()->subWeeks($w)->startOfWeek();
                        $end = $start->copy()->endOfWeek();
                        
                        $count = \App\Models\Jurnal::whereHas('siswa', function($q) use ($teacher) {
                                $q->where(['pembimbing_sekolah_id' => $teacher->id]);
                            })
                            ->whereIn('status', ['valid', 'invalid'])
                            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                            ->count();
                            
                        $weeksEvaluatedData[] = $count;
                    }

                    return [
                        'siswa_count' => \App\Models\Siswa::where(['pembimbing_sekolah_id' => $teacher->id])->count(),
                        'jurnal_pending' => \App\Models\Jurnal::whereHas('siswa', function($q) use ($teacher) {
                            $q->where(['pembimbing_sekolah_id' => $teacher->id]);
                        })->where(['status' => 'pending'])->count(),
                        'jurnal_masuk' => $jurnalMasuk,
                        'weeks_evaluated' => $weeksEvaluatedData,
                    ];
                });
                return view('dashboards.pembimbing-sekolah', compact('stats'));
            case 'pembimbing_dudi':
                $mentor = $user->pembimbingDudi;
                $stats = \Illuminate\Support\Facades\Cache::remember("dashboard_pembimbing_dudi_{$mentor->id}", 300, function() use ($mentor) {
                    return [
                        'siswa_count' => \App\Models\Siswa::where(['dudi_id' => $mentor->dudi_id])->count(),
                        'jurnal_pending' => \App\Models\Jurnal::whereHas('siswa', function($q) use ($mentor) {
                            $q->where(['dudi_id' => $mentor->dudi_id]);
                        })->where(['status' => 'pending'])->count(),
                    ];
                });
                return view('dashboards.pembimbing-dudi', compact('stats'));
            case 'kaprog':
                $today = \Carbon\Carbon::today();
                $stats = \Illuminate\Support\Facades\Cache::remember("dashboard_kaprog_{$user->id}_{$today->format('Y-m-d')}", 300, function() use ($today, $user) {
                    $baseSiswaQuery = \App\Models\Siswa::query();
                    $baseDudiQuery = \App\Models\Dudi::query();
                    $basePembimbingQuery = \App\Models\PembimbingSekolah::query();
                    $basePengajuanQuery = \App\Models\PengajuanPkl::query();

                    if ($user->konsentrasi_keahlian_id) {
                        $baseSiswaQuery->where('konsentrasi_keahlian_id' . '', $user->konsentrasi_keahlian_id);
                        $basePembimbingQuery->where('konsentrasi_keahlian_id' . '', $user->konsentrasi_keahlian_id);
                        
                        $userKonId = $user->konsentrasi_keahlian_id;
                        $baseDudiQuery->where(function($q) use ($userKonId) {
                            $q->where('konsentrasi_keahlian_id' . '', $userKonId)
                              ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                                  $sub->where('konsentrasi_keahlians.id', $userKonId);
                              });
                        });

                        $basePengajuanQuery->whereHas('siswa', function($q) use ($userKonId) {
                            $q->where('konsentrasi_keahlian_id' . '', $userKonId);
                        });
                    } elseif ($user->program_keahlian_id) {
                        $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id' . '', $user->program_keahlian_id)->pluck('id');
                        $baseSiswaQuery->whereIn('konsentrasi_keahlian_id' . '', $konsentrasiIds);
                        $basePembimbingQuery->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);

                        $baseDudiQuery->where(function($q) use ($konsentrasiIds) {
                            $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                              ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                                  $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                              });
                        });

                        $basePengajuanQuery->whereHas('siswa', function($q) use ($konsentrasiIds) {
                            $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
                        });
                    }

                    $activeStudentsCount = (clone $baseSiswaQuery)->where(['status_pkl' => 'sedang_pkl'])->count();
                    
                    // Absensi stats
                    $absensiTodayCount = \App\Models\Absensi::whereDate('tanggal', $today)
                        ->whereIn('siswa_id', (clone $baseSiswaQuery)->pluck('id'))
                        ->distinct('siswa_id')->count();
                    $attendanceRate = $activeStudentsCount > 0 ? round(($absensiTodayCount / $activeStudentsCount) * 100) : 0;
                    
                    $missingAttendance = (clone $baseSiswaQuery)->where(['status_pkl' => 'sedang_pkl'])
                        ->whereDoesntHave('absensi', function($q) use ($today) {
                            $q->whereDate('tanggal', $today);
                        })
                        ->take(5)
                        ->get(['nama_lengkap', 'kelas'])
                        ->map(fn($s) => ['nama_lengkap' => $s->nama_lengkap, 'kelas' => $s->kelas])
                        ->toArray();

                    // Jurnal stats
                    $jurnalTodayCount = \App\Models\Jurnal::whereDate('tanggal', $today)
                        ->whereIn('siswa_id', (clone $baseSiswaQuery)->pluck('id'))
                        ->distinct('siswa_id')->count();
                    $journalRate = $activeStudentsCount > 0 ? round(($jurnalTodayCount / $activeStudentsCount) * 100) : 0;
                    
                    $missingJournal = (clone $baseSiswaQuery)->where(['status_pkl' => 'sedang_pkl'])
                        ->whereDoesntHave('jurnal', function($q) use ($today) {
                            $q->whereDate('tanggal', $today);
                        })
                        ->take(5)
                        ->get(['nama_lengkap', 'kelas'])
                        ->map(fn($s) => ['nama_lengkap' => $s->nama_lengkap, 'kelas' => $s->kelas])
                        ->toArray();

                    return [
                        'total_siswa' => (clone $baseSiswaQuery)->count(),
                        'total_dudi' => (clone $baseDudiQuery)->count(),
                        'total_pembimbing' => (clone $basePembimbingQuery)->count(),
                        'pengajuan_menunggu' => (clone $basePengajuanQuery)->where(['status' => 'menunggu'])->count(),
                        'attendance_rate' => $attendanceRate,
                        'journal_rate' => $journalRate,
                        'missing_attendance' => $missingAttendance,
                        'missing_journal' => $missingJournal,
                    ];
                });

                $stats['missing_attendance'] = collect($stats['missing_attendance'])->map(fn($s) => (object)$s);
                $stats['missing_journal'] = collect($stats['missing_journal'])->map(fn($s) => (object)$s);

                return view('dashboards.kaprog', compact('stats'));
            case 'pokja':
                $stats = \Illuminate\Support\Facades\Cache::remember("dashboard_{$role}_{$user->id}", 300, function() use ($user) {
                    $startOfWeek = \Carbon\Carbon::now()->startOfWeek(1);
                    $endOfWeek = $startOfWeek->copy()->addDays(4);

                    $baseSiswaQuery = \App\Models\Siswa::query();
                    $baseDudiQuery = \App\Models\Dudi::query();
                    $basePembimbingQuery = \App\Models\PembimbingSekolah::query();

                    if ($user->konsentrasi_keahlian_id) {
                        $baseSiswaQuery->where('konsentrasi_keahlian_id' . '', $user->konsentrasi_keahlian_id);
                        $basePembimbingQuery->where('konsentrasi_keahlian_id' . '', $user->konsentrasi_keahlian_id);
                        
                        $userKonId = $user->konsentrasi_keahlian_id;
                        $baseDudiQuery->where(function($q) use ($userKonId) {
                            $q->where('konsentrasi_keahlian_id' . '', $userKonId)
                              ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                                  $sub->where('konsentrasi_keahlians.id', $userKonId);
                              });
                        });
                    } elseif ($user->program_keahlian_id) {
                        $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id' . '', $user->program_keahlian_id)->pluck('id');
                        $baseSiswaQuery->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
                        $basePembimbingQuery->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);

                        $baseDudiQuery->where(function($q) use ($konsentrasiIds) {
                            $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                              ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                                  $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                              });
                        });
                    }

                    $siswaIds = (clone $baseSiswaQuery)->pluck('id');

                    $jurnalCounts = \App\Models\Jurnal::whereBetween('tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                        ->whereIn('siswa_id', $siswaIds)
                        ->groupBy('tanggal')
                        ->selectRaw('tanggal, count(*) as total')
                        ->pluck('total', 'tanggal')
                        ->toArray();

                    $absensiCounts = \App\Models\Absensi::whereBetween('tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                        ->whereIn('siswa_id', $siswaIds)
                        ->where(['status' => 'hadir'])
                        ->groupBy('tanggal')
                        ->selectRaw('tanggal, count(*) as total')
                        ->pluck('total', 'tanggal')
                        ->toArray();

                    $weekJurnalData = [];
                    $weekAbsensiData = [];
                    for ($i = 0; $i < 5; $i++) {
                        $dateStr = $startOfWeek->copy()->addDays($i)->toDateString();
                        $weekJurnalData[] = $jurnalCounts[$dateStr] ?? 0;
                        $weekAbsensiData[] = $absensiCounts[$dateStr] ?? 0;
                    }

                    return [
                        'total_siswa' => (clone $baseSiswaQuery)->count(),
                        'total_dudi' => (clone $baseDudiQuery)->count(),
                        'total_pembimbing' => (clone $basePembimbingQuery)->count(),
                        'week_jurnal' => $weekJurnalData,
                        'week_absensi' => $weekAbsensiData,
                    ];
                });
                return view('dashboards.pokja', compact('stats'));
            case 'super_admin':
                $stats = \Illuminate\Support\Facades\Cache::remember("dashboard_{$role}", 300, function() {
                    return [
                        'total_siswa' => \App\Models\Siswa::count(),
                        'total_dudi' => \App\Models\Dudi::count(),
                        'total_pembimbing' => \App\Models\PembimbingSekolah::count(),
                    ];
                });
                return view('dashboards.super-admin', compact('stats'));
            default:
                abort(403, 'Unauthorized action.');
        }
    }

    public function bulkAcc(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user->role;
        if ($role === 'pembimbing_sekolah') {
            $teacher = $user->pembimbingSekolah;
            
            // ACC all pending jurnal
            \App\Models\Jurnal::whereHas('siswa', function($q) use ($teacher) {
                $q->where(['pembimbing_sekolah_id' => $teacher->id]);
            })->where(['approval_status' => 'pending'])->update([
                'status' => 'valid',
                'approval_status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            
            // ACC all pending absensi
            \App\Models\Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where(['pembimbing_sekolah_id' => $teacher->id]);
            })->where(['approval_status' => 'pending'])->update([
                'approval_status' => 'approved',
                'approved_by' => $user->id,
            ]);
            
            return response()->json(['message' => 'Semua Jurnal dan Absensi berhasil di-ACC (Rapid Testing Mode)']);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
