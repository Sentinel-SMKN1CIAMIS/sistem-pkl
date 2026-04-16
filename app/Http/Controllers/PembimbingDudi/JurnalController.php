<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;
use App\Models\Siswa;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function index()
    {
        $mentor = auth()->user()->pembimbingDudi;
        
        // Find all students assigned to this mentor's company
        $jurnals = Jurnal::whereHas('siswa', function($q) use ($mentor) {
                $q->where('dudi_id', $mentor->dudi_id);
            })
            ->with(['siswa', 'kompetensi'])
            ->latest('tanggal')
            ->paginate(15);

        return view('pembimbing-dudi.jurnal.index', compact('jurnals'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $request->validate([
            'status' => 'required|in:valid,invalid',
            'catatan_pembimbing' => 'nullable|string'
        ]);

        $jurnal->update([
            'status' => $request->status,
            'catatan_pembimbing' => $request->catatan_pembimbing
        ]);

        // Notify Student
        \App\Models\Notifikasi::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $jurnal->siswa->user_id,
            'judul' => 'Jurnal ' . ($request->status == 'valid' ? 'Divalidasi' : 'Ditolak'),
            'pesan' => 'Jurnal kegiatan Anda pada tanggal ' . $jurnal->tanggal . ' telah ' . ($request->status == 'valid' ? 'disetujui' : 'ditolak') . ' oleh mentor.',
            'tipe' => $request->status == 'valid' ? 'success' : 'warning'
        ]);

        return back()->with('success', 'Status jurnal berhasil diperbarui.');
    }
}
