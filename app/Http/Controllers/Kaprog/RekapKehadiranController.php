<?php

namespace App\Http\Controllers\Kaprog;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Services\EarlyWarningService;
use App\Services\PdfKopService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RekapKehadiranController extends Controller
{
    private function buildRekapData(Request $request, EarlyWarningService $earlyWarningService): array
    {
        $user = Auth::user();
        $program = ProgramKeahlian::find($user->program_keahlian_id);
        $concentrations = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)
            ->orderBy('kode')
            ->get();
        $allowedConcentrationIds = $concentrations->pluck('id')->all();

        $month = max(1, min(12, (int) $request->query('month', now()->month)));
        $year = max(2020, min(2035, (int) $request->query('year', now()->year)));
        $selectedConcentration = $request->query('konsentrasi_keahlian_id', 'semua');
        $selectedKelas = $request->query('kelas', 'semua');
        $riskFilter = $request->query('risk', 'semua');
        $search = trim((string) $request->query('search', ''));

        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $availableClasses = Siswa::whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->filter()
            ->values();

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

        $absensiSummary = Absensi::whereIn('siswa_id', $siswaIds)
            ->whereBetween('tanggal', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->selectRaw("siswa_id, status, COUNT(*) as total")
            ->groupBy('siswa_id', 'status')
            ->get()
            ->groupBy('siswa_id');

        $warningEval = $earlyWarningService->evaluateStudents(
            $allStudents->where('status_pkl', 'sedang_pkl')->values(),
            Carbon::today(),
            null
        );
        $warningsBySiswa = $warningEval['items']->keyBy('siswa_id');

        $allRows = $allStudents->map(function (Siswa $siswa) use ($absensiSummary, $warningsBySiswa) {
            $counts = $absensiSummary->get($siswa->id, collect())->pluck('total', 'status');
            $hadir = (int) ($counts->get('hadir', 0));
            $izin = (int) ($counts->get('izin', 0));
            $sakit = (int) ($counts->get('sakit', 0));
            $libur = (int) ($counts->get('libur', 0));
            $alpha = (int) ($counts->get('alpha', 0));
            $workTotal = $hadir + $izin + $sakit + $alpha;
            $rate = $workTotal > 0 ? round(($hadir / $workTotal) * 100) : 0;

            return [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'libur' => $libur,
                'alpha' => $alpha,
                'rate' => $rate,
                'warning' => $warningsBySiswa->get($siswa->id),
            ];
        });

        if ($riskFilter === 'berisiko') {
            $allRows = $allRows->filter(fn($r) => $r['warning'] !== null)->values();
        } elseif ($riskFilter === 'aman') {
            $allRows = $allRows->filter(fn($r) => $r['warning'] === null)->values();
        }

        $summaryStats = [
            'total_siswa' => $allStudents->count(),
            'total_berisiko' => $warningEval['total_at_risk'],
            'total_kritis' => $warningEval['critical_count'],
            'total_hadir' => $allRows->sum('hadir'),
            'total_alpha' => $allRows->sum('alpha'),
        ];

        return compact(
            'program',
            'concentrations',
            'availableClasses',
            'allRows',
            'summaryStats',
            'month',
            'year',
            'selectedConcentration',
            'selectedKelas',
            'riskFilter',
            'search',
            'periodStart'
        );
    }

    public function index(Request $request, EarlyWarningService $earlyWarningService)
    {
        if (!Auth::user()->program_keahlian_id) {
            return back()->with('error', 'Akun Kaprog Anda belum terhubung ke Program Keahlian manapun.');
        }

        $data = $this->buildRekapData($request, $earlyWarningService);
        $allRows = $data['allRows'];

        $perPage = (int) $request->query('per_page', 15);
        if (!in_array($perPage, [15, 25, 50], true)) {
            $perPage = 15;
        }
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $rows = new LengthAwarePaginator(
            $allRows->forPage($currentPage, $perPage)->values(),
            $allRows->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('kaprog.rekap-kehadiran.index', array_merge($data, [
            'rows' => $rows,
            'perPage' => $perPage,
        ]));
    }

    public function exportPdf(Request $request, EarlyWarningService $earlyWarningService)
    {
        if (!Auth::user()->program_keahlian_id) {
            abort(403, 'Akun Kaprog Anda belum terhubung ke Program Keahlian manapun.');
        }

        $data = $this->buildRekapData($request, $earlyWarningService);
        $kopData = PdfKopService::getKopData();

        $pdf = Pdf::loadView('kaprog.rekap-kehadiran.export-pdf', array_merge($data, $kopData, [
            'rows' => $data['allRows'],
        ]))->setPaper('a4', 'landscape');

        $programSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['program']?->kode ?? 'jurusan'));
        $fileName = sprintf('rekap-kehadiran-%s-%04d-%02d.pdf', $programSlug, $data['year'], $data['month']);

        return $pdf->download($fileName);
    }
}
