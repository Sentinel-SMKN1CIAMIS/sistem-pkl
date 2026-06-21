<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function index()
    {
        $mentor = auth()->user()->pembimbingDudi;
        
        if (!$mentor) {
            return redirect()->back()->with('error', 'Profil pembimbing DUDI tidak ditemukan.');
        }

        $students = Siswa::where('dudi_id', $mentor->dudi_id)
            ->with(['konsentrasiKeahlian', 'pembimbingSekolah', 'pembimbingSekolahUmum'])
            ->withCount(['jurnal', 'absensi'])
            ->get();

        return view('pembimbing-dudi.siswa.index', compact('students'));
    }
}
