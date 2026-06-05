<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class KaprogController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // If user is kaprog without assigned class, show no data
        if (!$user->konsentrasi_keahlian_id) {
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

        // Filter siswas by Kaprog's assigned konsentrasi keahlian
        $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'konsentrasiKeahlian'])
            ->where('konsentrasi_keahlian_id', $user->konsentrasi_keahlian_id)
            ->latest()
            ->paginate(15);
            
        // Stats for Kaprog's class only
        $totalSiswa = Siswa::where('konsentrasi_keahlian_id', $user->konsentrasi_keahlian_id)->count();
        $siswaPkl = Siswa::where('konsentrasi_keahlian_id', $user->konsentrasi_keahlian_id)
            ->whereNotNull('dudi_id')
            ->count();
        $siswaBelumPkl = $totalSiswa - $siswaPkl;

        return view('kaprog.laporan.index', compact('siswas', 'totalSiswa', 'siswaPkl', 'siswaBelumPkl'));
    }
}
