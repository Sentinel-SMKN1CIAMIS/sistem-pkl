<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class KaprogController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'konsentrasiKeahlian'])
            ->latest()
            ->paginate(15);
            
        // Stats
        $totalSiswa = Siswa::count();
        $siswaPkl = Siswa::whereNotNull('dudi_id')->count();
        $siswaBelumPkl = $totalSiswa - $siswaPkl;

        return view('kaprog.laporan.index', compact('siswas', 'totalSiswa', 'siswaPkl', 'siswaBelumPkl'));
    }
}
