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

        $students = Siswa::where('pembimbing_dudi_id', $mentor->id)
            ->with(['konsentrasiKeahlian', 'pembimbingSekolah'])
            ->withCount(['jurnal', 'absensi'])
            ->get();

        return view('pembimbing-dudi.siswa.index', compact('students'));
    }
}
