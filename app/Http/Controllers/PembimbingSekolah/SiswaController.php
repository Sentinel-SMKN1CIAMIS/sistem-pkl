<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil pembimbing sekolah tidak ditemukan.');
        }

        $query = Siswa::where('pembimbing_sekolah_id', $teacher->id)
            ->with(['konsentrasiKeahlian', 'dudi'])
            ->withCount(['jurnal', 'absensi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        return view('pembimbing-sekolah.siswa.index', compact('students'));
    }
}
