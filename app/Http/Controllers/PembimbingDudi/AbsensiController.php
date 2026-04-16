<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Absensi;


class AbsensiController extends Controller
{
    public function index()
    {
        $mentor = auth()->user()->pembimbingDudi;
        
        $absensis = Absensi::whereHas('siswa', function($q) use ($mentor) {
                $q->where('dudi_id', $mentor->dudi_id);
            })
            ->with('siswa')
            ->latest('tanggal')
            ->paginate(15);

        return view('pembimbing-dudi.absensi.index', compact('absensis'));
    }
}
