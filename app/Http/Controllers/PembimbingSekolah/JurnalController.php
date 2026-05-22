<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;


class JurnalController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        // Find all students assigned to this teacher
        $jurnals = Jurnal::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
            })
            ->with(['siswa', 'kompetensi'])
            ->latest('tanggal')
            ->paginate(15);

        return view('pembimbing-sekolah.jurnal.index', compact('jurnals'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $request->validate([
            'catatan_guru' => 'nullable|string'
        ]);

        $jurnal->update([
            'catatan_guru' => $request->catatan_guru
        ]);

        return back()->with('success', 'Saran / Komentar berhasil disimpan.');
    }
}
