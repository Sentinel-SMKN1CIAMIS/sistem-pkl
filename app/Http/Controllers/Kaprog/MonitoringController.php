<?php

namespace App\Http\Controllers\Kaprog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembimbingSekolah;
use App\Models\Siswa;
use App\Models\MonitoringPembimbing;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Services\PdfKopService;
use Barryvdh\DomPDF\Facade\Pdf;

class MonitoringController extends Controller
{
    private function buildMentorsData(Request $request): array
    {
        $user = Auth::user();
        $allowedConcentrationIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $allMentors = $query->latest()->get()->map(function ($mentor) {
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

        if ($request->filled('status')) {
            $status = $request->status;
            $allMentors = $allMentors->filter(fn($mentor) => $mentor->activity_status === $status)->values();
        }

        $stats = [
            'total' => $allMentors->count(),
            'aktif' => $allMentors->where('activity_status', 'aktif')->count(),
            'kurang_aktif' => $allMentors->where('activity_status', 'kurang_aktif')->count(),
            'tidak_pernah_login' => $allMentors->where('activity_status', 'tidak_pernah_login')->count(),
        ];

        $program = ProgramKeahlian::find($user->program_keahlian_id);

        return compact('allMentors', 'stats', 'program');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->program_keahlian_id) {
            return back()->with('error', 'Anda tidak memiliki wewenang program keahlian.');
        }

        $data = $this->buildMentorsData($request);
        $allMentors = $data['allMentors'];

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $mentors = new LengthAwarePaginator(
            $allMentors->forPage($currentPage, $perPage)->values(),
            $allMentors->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('kaprog.monitoring.index', [
            'mentors' => $mentors,
            'stats' => $data['stats'],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if (!$user->program_keahlian_id) {
            abort(403, 'Anda tidak memiliki wewenang program keahlian.');
        }

        $data = $this->buildMentorsData($request);
        $kopData = PdfKopService::getKopData();

        $pdf = Pdf::loadView('pdf.monitoring-pembimbing', array_merge($kopData, [
            'mentors' => $data['allMentors'],
            'stats' => $data['stats'],
            'scopeTitle' => 'Program Keahlian ' . ($data['program']?->nama ?? '-'),
            'signerTitle' => 'Kepala Program Keahlian',
            'signerName' => $user->name,
        ]))->setPaper('a4', 'landscape');

        return $pdf->download('rekap-monitoring-pembimbing-' . now()->format('Y-m-d') . '.pdf');
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
