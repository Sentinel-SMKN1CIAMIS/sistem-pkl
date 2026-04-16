<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BukuPanduan;

class PanduanController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;
        
        // Show general reports and those specific to student department
        $panduans = BukuPanduan::where(function($q) use ($siswa) {
                $q->where('tipe', 'siswa')
                  ->orWhere('tipe', 'umum');
            })
            ->where(function($q) use ($siswa) {
                $q->whereNull('konsentrasi_keahlian_id')
                  ->orWhere('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id);
            })
            ->latest()
            ->get();

        return view('siswa.panduan.index', compact('panduans'));
    }
}
