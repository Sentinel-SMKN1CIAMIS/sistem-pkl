<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Absensi;


class AbsensiController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        $absensis = Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
            })
            ->with('siswa')
            ->latest('tanggal')
            ->paginate(15);

        return view('pembimbing-sekolah.absensi.index', compact('absensis'));
    }
}
