<?php

namespace App\Http\Controllers\Kaprog;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Jurnal;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Services\EarlyWarningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringSiswaController extends Controller
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

        $search = trim((string) $request->query('search', ''));
        $selectedConcentration = $request->query('konsentrasi_keahlian_id', 'semua');
        $selectedKelas = $request->query('kelas', 'semua');
        $selectedDudi = $request->query('dudi_id', 'semua');
        $todayStatusFilter = $request->query('status_hari_ini', 'semua');
        $riskFilter = $request->query('risk', 'semua');

        $todayStr = Carbon::today()->toDateString();

        // Filter dropdown options scoped to Kaprog's Program Keahlian
        $availableClasses = Siswa::whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)
            ->distinct()->orderBy('kelas')->pluck('kelas')->filter()->values();
        $availableDudis = Dudi::whereHas('siswa', fn($q) => $q->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds))
            ->orderBy('nama')->get(['id', 'nama']);

        $query = Siswa::with([
            'konsentrasiKeahlian:id,kode,nama',
            'dudi:id,nama,kota',
            'pembimbingSekolah:id,nama_lengkap',
        ])->withCount([
            'jurnal as total_jurnal_count',
            'jurnal as pending_jurnal_count' => fn($q) => $q->where('approval_status', 'pending'),
            'jurnal as valid_jurnal_count' => fn($q) => $q->where(fn($sub) => $sub->where('approval_status', 'approved')->orWhere('status', 'valid')),
            'absensi as total_hadir_count' => fn($q) => $q->where('status', 'hadir'),
        ])->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds);

        if ($selectedConcentration !== 'semua' && in_array((int) $selectedConcentration, $allowedConcentrationIds, true)) {
            $query->where('konsentrasi_keahlian_id', (int) $selectedConcentration);
        }

        if ($selectedKelas !== 'semua') {
            $query->where('kelas', $selectedKelas);
        }

        if ($selectedDudi !== 'semua') {
            $query->where('dudi_id', (int) $selectedDudi);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhereHas('dudi', fn($dq) => $dq->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($todayStatusFilter === 'hadir') {
            $query->whereHas('absensi', fn($q) => $q->whereDate('tanggal', $todayStr)->where('status', 'hadir'));
        } elseif ($todayStatusFilter === 'belum_absen') {
            $query->whereDoesntHave('absensi', fn($q) => $q->whereDate('tanggal', $todayStr));
        } elseif ($todayStatusFilter === 'izin_sakit_libur') {
            $query->whereHas('absensi', fn($q) => $q->whereDate('tanggal', $todayStr)->whereIn('status', ['izin', 'sakit', 'libur']));
        }

        // Compute Early Warning across all active students in this program
        $allActiveInProgram = Siswa::whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)
            ->where('status_pkl', 'sedang_pkl')
            ->get();
        $warningEval = $earlyWarningService->evaluateStudents($allActiveInProgram, Carbon::today(), null);
        $warningsBySiswa = $warningEval['items']->keyBy('siswa_id');
        $atRiskIds = $warningsBySiswa->keys()->all();

        if ($riskFilter === 'berisiko') {
            $query->whereIn('id', empty($atRiskIds) ? [0] : $atRiskIds);
        } elseif ($riskFilter === 'aman' && !empty($atRiskIds)) {
            $query->whereNotIn('id', $atRiskIds);
        }

        $students = $query->orderBy('kelas')->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        // Batch fetch today's attendance for current page students
        $pageSiswaIds = $students->pluck('id')->all();
        $todayAbsensiBySiswa = Absensi::whereIn('siswa_id', $pageSiswaIds)
            ->whereDate('tanggal', $todayStr)
            ->get()
            ->keyBy('siswa_id');

        $summary = [
            'total_siswa' => Siswa::whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds)->count(),
            'hadir_hari_ini' => Absensi::whereDate('tanggal', $todayStr)
                ->where('status', 'hadir')
                ->whereHas('siswa', fn($q) => $q->whereIn('konsentrasi_keahlian_id', $allowedConcentrationIds))
                ->count(),
            'berisiko' => $warningEval['total_at_risk'],
        ];

        return view('kaprog.monitoring-siswa.index', compact(
            'program',
            'concentrations',
            'availableClasses',
            'availableDudis',
            'students',
            'todayAbsensiBySiswa',
            'warningsBySiswa',
            'summary',
            'search',
            'selectedConcentration',
            'selectedKelas',
            'selectedDudi',
            'todayStatusFilter',
            'riskFilter'
        ));
    }

    public function show(Siswa $siswa, EarlyWarningService $earlyWarningService)
    {
        $user = Auth::user();
        if (!$user->program_keahlian_id) {
            abort(403, 'Akun Kaprog Anda belum terhubung ke Program Keahlian manapun.');
        }

        $allowedConcentrationIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)
            ->pluck('id')
            ->all();

        if (!in_array((int) $siswa->konsentrasi_keahlian_id, $allowedConcentrationIds, true)) {
            abort(403, 'Anda tidak memiliki wewenang untuk memantau siswa dari program keahlian lain.');
        }

        $siswa->load(['konsentrasiKeahlian', 'dudi', 'pembimbingSekolah', 'pembimbingSekolahUmum', 'pembimbingDudi']);

        $jurnals = Jurnal::with(['kompetensi', 'tujuanPembelajaran'])
            ->where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'jurnal_page');

        $absensis = Absensi::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'absensi_page');

        $attendanceCounts = Absensi::where('siswa_id', $siswa->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $warningEval = $earlyWarningService->evaluateStudents(collect([$siswa]), Carbon::today(), null);
        $warning = $warningEval['items']->first();

        return view('kaprog.monitoring-siswa.show', compact(
            'siswa',
            'jurnals',
            'absensis',
            'attendanceCounts',
            'warning'
        ));
    }
}
