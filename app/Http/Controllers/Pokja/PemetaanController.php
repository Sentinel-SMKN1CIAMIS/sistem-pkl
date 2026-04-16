<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use Illuminate\Http\Request;

class PemetaanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        
        $siswas = Siswa::with(['dudi', 'pembimbingSekolah', 'pembimbingDudi', 'konsentrasiKeahlian'])
            ->when($search, function($query) use ($search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalSiswa = Siswa::count();
        $terpetakan = Siswa::whereNotNull('dudi_id')
            ->whereNotNull('pembimbing_sekolah_id')
            ->whereNotNull('pembimbing_dudi_id')
            ->count();
        
        return view('pokja.pemetaan.index', compact('siswas', 'totalSiswa', 'terpetakan'));
    }
}
