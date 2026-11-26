<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\KunjunganMonitoring;
use App\Models\PembimbingSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pembimbingId = $request->query('pembimbing_sekolah_id', 'semua');
        $dudiId = $request->query('dudi_id', 'semua');
        $month = $request->query('month', '');
        $search = trim((string) $request->query('search', ''));

        $query = KunjunganMonitoring::with([
            'pembimbingSekolah:id,nama_lengkap,nip,konsentrasi_keahlian_id',
            'dudi:id,nama,kota,konsentrasi_keahlian_id',
        ]);

        // Scope Kaprog to their Program Keahlian
        if ($user->role === 'kaprog' && $user->program_keahlian_id) {
            $allowedIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->all();
            $query->whereHas('pembimbingSekolah', fn($q) => $q->whereIn('konsentrasi_keahlian_id', $allowedIds));
        }

        if ($pembimbingId !== 'semua') {
            $query->where('pembimbing_sekolah_id', (int) $pembimbingId);
        }

        if ($dudiId !== 'semua') {
            $query->where('dudi_id', (int) $dudiId);
        }

        if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month)) {
            [$y, $m] = explode('-', $month);
            $query->whereYear('tanggal_kunjungan', (int) $y)->whereMonth('tanggal_kunjungan', (int) $m);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('catatan_monitoring', 'like', "%{$search}%")
                    ->orWhereHas('pembimbingSekolah', fn($pq) => $pq->where('nama_lengkap', 'like', "%{$search}%"))
                    ->orWhereHas('dudi', fn($dq) => $dq->where('nama', 'like', "%{$search}%"));
            });
        }

        $kunjungans = $query->orderByDesc('tanggal_kunjungan')->orderByDesc('id')->paginate(15)->withQueryString();
        $teachers = PembimbingSekolah::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']);
        $dudis = Dudi::orderBy('nama')->get(['id', 'nama']);

        $totalKunjungan = KunjunganMonitoring::count();
        $totalBulanIni = KunjunganMonitoring::whereYear('tanggal_kunjungan', now()->year)
            ->whereMonth('tanggal_kunjungan', now()->month)
            ->count();

        return view('pokja.kunjungan.index', compact(
            'kunjungans',
            'teachers',
            'dudis',
            'totalKunjungan',
            'totalBulanIni',
            'pembimbingId',
            'dudiId',
            'month',
            'search'
        ));
    }
}
