<?php

namespace App\Http\Controllers\Kaprog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembimbingSekolah;
use App\Models\Siswa;
use App\Models\MonitoringPembimbing;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->program_keahlian_id) {
            return back()->with('error', 'Anda tidak memiliki wewenang program keahlian.');
        }

        $allowedConcentrationIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();

        // Query only Pembimbing Sekolah belonging to the Kaprog's program or teaching/guiding students in the Kaprog's program
        $query = PembimbingSekolah::with(['user', 'konsentrasiKeahlian'])
            ->where(function($q) use ($allowedConcentrationIds) {
                $q->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)
                  ->orWhereHas('siswa', function($s) use ($allowedConcentrationIds) {
                      $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                  })
                  ->orWhereHas('siswaUmum', function($s) use ($allowedConcentrationIds) {
                      $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                  });
            })
            ->withCount([
                'siswa' => function($q) use ($allowedConcentrationIds) {
                    $q->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                },
                'siswaUmum' => function($q) use ($allowedConcentrationIds) {
                    $q->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                },
                'jurnal as total_jurnals_count' => function($q) use ($allowedConcentrationIds) {
                    $q->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                        $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                    });
                },
                'jurnalUmum as total_jurnals_umum_count' => function($q) use ($allowedConcentrationIds) {
                    $q->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                        $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                    });
                },
                'jurnal as pending_jurnals_count' => function($q) use ($allowedConcentrationIds) {
                    $q->where('approval_status', 'pending')
                      ->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                          $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                      });
                },
                'jurnalUmum as pending_jurnals_umum_count' => function($q) use ($allowedConcentrationIds) {
                    $q->where('approval_status', 'pending')
                      ->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                          $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                      });
                },
                'absensi as total_absensis_count' => function($q) use ($allowedConcentrationIds) {
                    $q->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                        $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                    });
                },
                'absensiUmum as total_absensis_umum_count' => function($q) use ($allowedConcentrationIds) {
                    $q->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                        $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                    });
                },
                'absensi as pending_absensis_count' => function($q) use ($allowedConcentrationIds) {
                    $q->where('approval_status', 'pending')
                      ->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                          $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                      });
                },
                'absensiUmum as pending_absensis_umum_count' => function($q) use ($allowedConcentrationIds) {
                    $q->where('approval_status', 'pending')
                      ->whereHas('siswa', function($s) use ($allowedConcentrationIds) {
                          $s->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);
                      });
                }
            ]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Tipe filter
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $mentors = $query->latest()->get();

        // Enrich & calculate status dynamically based only on Kaprog's students
        $mentors = $mentors->map(function ($mentor) {
            $mentor->siswa_count = $mentor->siswa_count + $mentor->siswa_umum_count;
            $mentor->total_jurnals_count = $mentor->total_jurnals_count + $mentor->total_jurnals_umum_count;
            $mentor->pending_jurnals_count = $mentor->pending_jurnals_count + $mentor->pending_jurnals_umum_count;
            $mentor->total_absensis_count = $mentor->total_absensis_count + $mentor->total_absensis_umum_count;
            $mentor->pending_absensis_count = $mentor->pending_absensis_count + $mentor->pending_absensis_umum_count;

            $lastLogin = $mentor->user?->last_login_at;
            if (!$lastLogin) {
                $mentor->activity_status = 'tidak_pernah_login';
            } elseif ($lastLogin->diffInDays(now()) > 3 || $mentor->pending_jurnals_count > 0 || $mentor->pending_absensis_count > 0) {
                $mentor->activity_status = 'kurang_aktif';
            } else {
                $mentor->activity_status = 'aktif';
            }
            return $mentor;
        });

        // Filter by status if requested
        if ($request->filled('status')) {
            $status = $request->status;
            $mentors = $mentors->filter(function($mentor) use ($status) {
                return $mentor->activity_status === $status;
            });
        }

        // Statistics for dashboard based on Kaprog's allowed program
        $stats = [
            'total' => $mentors->count(),
            'aktif' => $mentors->where('activity_status', 'aktif')->count(),
            'kurang_aktif' => $mentors->where('activity_status', 'kurang_aktif')->count(),
            'tidak_pernah_login' => $mentors->where('activity_status', 'tidak_pernah_login')->count(),
        ];

        return view('kaprog.monitoring.index', compact('mentors', 'stats'));
    }

    public function show(PembimbingSekolah $pembimbingSekolah)
    {
        $user = Auth::user();
        if (!$user->program_keahlian_id) {
            abort(403, 'Anda tidak memiliki wewenang program keahlian.');
        }

        $allowedConcentrationIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();

        // Scope validation check: Is this advisor related to this Kaprog's department?
        $isRelated = ($pembimbingSekolah->konsentrasi_keahlian_id && in_array($pembimbingSekolah->konsentrasi_keahlian_id, $allowedConcentrationIds))
            || $pembimbingSekolah->siswa()->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)->exists()
            || $pembimbingSekolah->siswaUmum()->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)->exists();

        if (!$isRelated) {
            abort(403, 'Anda tidak memiliki akses ke data pembimbing ini.');
        }

        $pembimbingSekolah->load(['user', 'konsentrasiKeahlian']);

        // Fetch students under this advisor restricted to Kaprog's program keahlian
        $students = Siswa::whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)
            ->where(function($q) use ($pembimbingSekolah) {
                $q->where('pembimbing_sekolah_id', $pembimbingSekolah->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $pembimbingSekolah->id);
            })
            ->withCount([
                'jurnal as total_jurnals_count',
                'jurnal as pending_jurnals_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'absensi as total_absensis_count',
                'absensi as pending_absensis_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                }
            ])
            ->with(['dudi'])
            ->get();

        // Load monitoring notes/logs for this advisor
        $monitoringLogs = MonitoringPembimbing::where(['pembimbing_sekolah_id' => $pembimbingSekolah->id])
            ->with('pokjaUser')
            ->latest()
            ->get();

        // Calculate advisor overall status for the header
        $lastLogin = $pembimbingSekolah->user?->last_login_at;
        $pendingJurnals = $students->sum(function($s) { return $s->pending_jurnals_count; });
        $pendingAbsences = $students->sum(function($s) { return $s->pending_absensis_count; });

        if (!$lastLogin) {
            $activityStatus = 'tidak_pernah_login';
        } elseif ($lastLogin->diffInDays(now()) > 3 || $pendingJurnals > 0 || $pendingAbsences > 0) {
            $activityStatus = 'kurang_aktif';
        } else {
            $activityStatus = 'aktif';
        }

        return view('kaprog.monitoring.show', compact(
            'pembimbingSekolah', 
            'students', 
            'monitoringLogs', 
            'activityStatus',
            'pendingJurnals',
            'pendingAbsences'
        ));
    }
}
