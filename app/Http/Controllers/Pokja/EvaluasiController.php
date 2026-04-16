<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\LaporanPkl;
use Illuminate\Http\Request;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        
        $siswas = Siswa::with(['dudi', 'laporan', 'pembimbingSekolah'])
            ->withCount([
                'jurnal as total_jurnal',
                'jurnal as valid_jurnal' => function ($query) {
                    $query->where('status', 'valid');
                },
                'absensi'
            ])
            ->when($search, function($query) use ($search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Calculate some stats for the header
        $totalSiswa = Siswa::count();
        $laporanMasuk = LaporanPkl::count();
        $rataJurnal = Siswa::withCount(['jurnal' => function($q) { $q->where('status', 'valid'); }])->get()->avg('jurnal_count');
        
        return view('pokja.evaluasi.index', compact('siswas', 'totalSiswa', 'laporanMasuk', 'rataJurnal'));
    }
}
