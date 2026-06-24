<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PembimbingSekolah;
use App\Models\Siswa;
use App\Models\Jurnal;
use App\Models\Absensi;
use App\Models\MonitoringPembimbing;
use App\Models\ProgramKeahlian;


class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = PembimbingSekolah::with(['user', 'konsentrasiKeahlian'])
            ->withCount([
                'siswa',
                'siswaUmum',
                'jurnal as total_jurnals_count',
                'jurnalUmum as total_jurnals_umum_count',
                'jurnal as pending_jurnals_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'jurnalUmum as pending_jurnals_umum_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'absensi as total_absensis_count',
                'absensiUmum as total_absensis_umum_count',
                'absensi as pending_absensis_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'absensiUmum as pending_absensis_umum_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                }
            ]);

        // Handle search by name or NIP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Handle filter by Tipe Pembimbing
        if ($request->filled('tipe')) {
            $query->where('tipe' . '', $request->tipe);
        }

        // Handle filter by Program Keahlian
        if ($request->filled('program_keahlian_id')) {
            $programKeahlianId = $request->program_keahlian_id;
            $query->whereHas('konsentrasiKeahlian', function($q) use ($programKeahlianId) {
                $q->where('program_keahlian_id' . '', $programKeahlianId);
            });
        }

        // Get all first to calculate status dynamically
        $mentors = $query->latest()->get();

        // Map and enrich status
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

        // Calculate card statistics across ALL mentors (without filters)
        $allMentorsForStats = PembimbingSekolah::with(['user'])
            ->withCount([
                'jurnal as pending_jurnals_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'jurnalUmum as pending_jurnals_umum_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'absensi as pending_absensis_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                },
                'absensiUmum as pending_absensis_umum_count' => function ($q) {
                    $q->where('approval_status', 'pending');
                }
            ])->get();

        $stats = [
            'total' => $allMentorsForStats->count(),
            'aktif' => 0,
            'kurang_aktif' => 0,
            'tidak_pernah_login' => 0,
        ];

        foreach ($allMentorsForStats as $m) {
            $pendingJurnals = $m->pending_jurnals_count + $m->pending_jurnals_umum_count;
            $pendingAbsences = $m->pending_absensis_count + $m->pending_absensis_umum_count;
            
            $lastLogin = $m->user?->last_login_at;
            if (!$lastLogin) {
                $stats['tidak_pernah_login']++;
            } elseif ($lastLogin->diffInDays(now()) > 3 || $pendingJurnals > 0 || $pendingAbsences > 0) {
                $stats['kurang_aktif']++;
            } else {
                $stats['aktif']++;
            }
        }

        $programKeahlians = ProgramKeahlian::orderBy('nama' . '')->get();

        return view('pokja.monitoring.index', compact('mentors', 'stats', 'programKeahlians'));
    }

    public function show(PembimbingSekolah $pembimbingSekolah)
    {
        $pembimbingSekolah->load(['user', 'konsentrasiKeahlian']);
        
        // Fetch students under this advisor with their individual stats
        $students = Siswa::where(function($q) use ($pembimbingSekolah) {
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

        return view('pokja.monitoring.show', compact(
            'pembimbingSekolah', 
            'students', 
            'monitoringLogs', 
            'activityStatus',
            'pendingJurnals',
            'pendingAbsences'
        ));
    }

    public function storeNote(Request $request, PembimbingSekolah $pembimbingSekolah)
    {
        $request->validate([
            'catatan' => 'required|string|min:5',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        MonitoringPembimbing::create([
            'pembimbing_sekolah_id' => $pembimbingSekolah->id,
            'pokja_user_id' => \Illuminate\Support\Facades\Auth::id(),
            'tanggal' => now()->toDateString(),
            'catatan' => $request->catatan,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Catatan monitoring / evaluasi pembimbing berhasil disimpan.');
    }
}
