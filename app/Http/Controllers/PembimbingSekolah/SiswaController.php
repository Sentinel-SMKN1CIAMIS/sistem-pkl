<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil pembimbing sekolah tidak ditemukan.');
        }

        $students = Siswa::where('pembimbing_sekolah_id', $teacher->id)
            ->with(['konsentrasiKeahlian', 'dudi'])
            ->withCount(['jurnal', 'absensi'])
            ->get();

        return view('pembimbing-sekolah.siswa.index', compact('students'));
    }
}
