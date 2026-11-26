<?php

namespace App\Http\Controllers\Kaprog;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Services\EarlyWarningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapKehadiranController extends Controller
{
    public function index(Request $request, EarlyWarningService $earlyWarningService)
    {
        $user = Auth::user();
        if (!$user->program_keahlian_id) {
            return back()->with('error', 'Akun Kaprog Anda belum terhubung ke Program Keahlian manapun.');
        }

        $program = ProgramKeahlian::find($user->program_keahlian_id);
        $concentrations = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)
            ->orderBy('kode')
            ->get();
        $allowedConcentrationIds = $concentrations->pluck('id')->all();

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $selectedConcentration = $request->query('konsentrasi_keahlian_id', 'semua');
        $selectedKelas = $request->query('kelas', 'semua');
        $riskFilter = $request->query('risk', 'semua');
        $search = trim((string) $request->query('search', ''));

        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Available classes across ALL students in Kaprog's Program Keahlian
        $availableClasses = Siswa::whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->filter()
            ->values();

        // Query ALL students in Kaprog's Program Keahlian (NOT limited to personally supervised students)
        $studentsQuery = Siswa::with([
            'konsentrasiKeahlian:id,kode,nama',
            'dudi:id,nama',
            'pembimbingSekolah:id,nama_lengkap',
        ])->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);

        if ($selectedConcentration !== 'semua' && in_array((int) $selectedConcentration, $allowedConcentrationIds, true)) {
            $studentsQuery->where('konsentrasi_keahlian_id', (int) $selectedConcentration);
        }

        if ($selectedKelas !== 'semua') {
            $studentsQuery->where('kelas', $selectedKelas);
        }

        if ($search !== '') {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhereHas('dudi', fn($dq) => $dq->where('nama', 'like', "%{$search}%"));
            });
        }

        $allStudents = $studentsQuery->orderBy('kelas')->orderBy('nama_lengkap')->get();
        $siswaIds = $allStudents->pluck('id')->all();

        // Aggregate attendance counts per student for the selected month/year
        $absensiSummary = Absensi::whereIn('siswa_id', $siswaIds)
            ->whereBetween('tanggal', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->selectRaw("siswa_id, status, COUNT(*) as total")
            ->groupBy('siswa_id', 'status')
            ->get()
            ->groupBy('siswa_id');

        // Evaluate Early Warning for all students in this program
        $warningEval = $earlyWarningService->evaluateStudents(
            $allStudents->where('status_pkl', 'sedang_pkl')->values(),
            Carbon::today(),
            null
        );
        $warningsBySiswa = $warningEval['items']->keyBy('siswa_id');

        $rows = $allStudents->map(function (Siswa $siswa) use ($absensiSummary, $warningsBySiswa) {
            $counts = $absensiSummary->get($siswa->id, collect())->pluck('total', 'status');
            $hadir = (int) ($counts->get('hadir', 0));
            $izin = (int) ($counts->get('izin', 0));
            $sakit = (int) ($counts->get('sakit', 0));
            $libur = (int) ($counts->get('libur', 0));
            $alpha = (int) ($counts->get('alpha', 0));
            $workTotal = $hadir + $izin + $sakit + $alpha;
            $rate = $workTotal > 0 ? round(($hadir / $workTotal) * 100) : 0;

            $warning = $warningsBySiswa->get($siswa->id);

            return [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'libur' => $libur,
                'alpha' => $alpha,
                'rate' => $rate,
                'warning' => $warning,
            ];
        });

        if ($riskFilter === 'berisiko') {
            $rows = $rows->filter(fn($r) => $r['warning'] !== null)->values();
        } elseif ($riskFilter === 'aman') {
            $rows = $rows->filter(fn($r) => $r['warning'] === null)->values();
        }

        $summaryStats = [
            'total_siswa' => $allStudents->count(),
            'total_berisiko' => $warningEval['total_at_risk'],
            'total_kritis' => $warningEval['critical_count'],
            'total_hadir' => $rows->sum('hadir'),
            'total_alpha' => $rows->sum('alpha'),
        ];

        return view('kaprog.rekap-kehadiran.index', compact(
            'program',
            'concentrations',
            'availableClasses',
            'rows',
            'summaryStats',
            'month',
            'year',
            'selectedConcentration',
            'selectedKelas',
            'riskFilter',
            'search'
        ));
    }
}
