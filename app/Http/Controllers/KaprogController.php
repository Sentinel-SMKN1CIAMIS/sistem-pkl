<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KaprogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // If user is kaprog without assigned program, show no data
        if (!$user->program_keahlian_id) {
            $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'pembimbingSekolahUmum', 'konsentrasiKeahlian'])
                ->where('konsentrasi_keahlian_id', -1) // Non-existent ID to return empty paginated result
                ->latest()
                ->paginate(15);
            
            return view('kaprog.laporan.index', [
                'siswas' => $siswas,
                'totalSiswa' => 0,
                'siswaPkl' => 0,
                'siswaBelumPkl' => 0,
                'kelasOptions' => collect(),
            ]);
        }

        // Filter siswas by Kaprog's assigned program keahlian (all concentrations under it)
        $allowedIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();

        // Get class options in this program
        $kelasOptions = Siswa::whereIn('konsentrasi_keahlian_id', $allowedIds)
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $query = Siswa::with(['dudi', 'pembimbingSekolah', 'pembimbingSekolahUmum', 'konsentrasiKeahlian'])
            ->whereIn('konsentrasi_keahlian_id', $allowedIds);

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $siswas = (clone $query)->latest()->paginate(15)->withQueryString();
            
        // Stats
        $totalSiswa = (clone $query)->count();
        $siswaPkl = (clone $query)->whereNotNull('dudi_id')->count();
        $siswaBelumPkl = $totalSiswa - $siswaPkl;

        return view('kaprog.laporan.index', compact('siswas', 'totalSiswa', 'siswaPkl', 'siswaBelumPkl', 'kelasOptions'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        if (!$user->program_keahlian_id) {
            return back()->with('error', 'Anda tidak memiliki wewenang program keahlian.');
        }

        $allowedIds = KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();

        $query = Siswa::with(['dudi', 'pembimbingSekolah', 'pembimbingSekolahUmum', 'konsentrasiKeahlian'])
            ->whereIn('konsentrasi_keahlian_id', $allowedIds);

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $siswas = $query->orderBy('nama_lengkap')->get();
        $program = ProgramKeahlian::find($user->program_keahlian_id);

        $pdf = Pdf::loadView('kaprog.laporan.export', [
            'siswas' => $siswas,
            'program' => $program,
            'kelas' => $request->kelas,
        ]);

        $fileName = 'rekap-penempatan-pkl-' . 
            ($program ? strtolower($program->kode) : 'program') . 
            ($request->filled('kelas') ? '-' . strtolower(str_replace(' ', '-', $request->kelas)) : '') . 
            '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }
}
