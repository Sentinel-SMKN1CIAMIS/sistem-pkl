<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class KaprogController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // If user is kaprog without assigned program, show no data
        if (!$user->program_keahlian_id) {
            $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'konsentrasiKeahlian'])
                ->where('konsentrasi_keahlian_id', -1) // Non-existent ID to return empty paginated result
                ->latest()
                ->paginate(15);
            
            return view('kaprog.laporan.index', [
                'siswas' => $siswas,
                'totalSiswa' => 0,
                'siswaPkl' => 0,
                'siswaBelumPkl' => 0,
            ]);
        }

        // Filter siswas by Kaprog's assigned program keahlian (all concentrations under it)
        $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();

        $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'konsentrasiKeahlian'])
            ->whereIn('konsentrasi_keahlian_id', $allowedIds)
            ->latest()
            ->paginate(15);
            
        // Stats for Kaprog's program only
        $totalSiswa = Siswa::whereIn('konsentrasi_keahlian_id', $allowedIds)->count();
        $siswaPkl = Siswa::whereIn('konsentrasi_keahlian_id', $allowedIds)
            ->whereNotNull('dudi_id')
            ->count();
        $siswaBelumPkl = $totalSiswa - $siswaPkl;

        return view('kaprog.laporan.index', compact('siswas', 'totalSiswa', 'siswaPkl', 'siswaBelumPkl'));
    }
}
