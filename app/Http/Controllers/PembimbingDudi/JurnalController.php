<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;
use App\Models\Siswa;


class JurnalController extends Controller
{
    public function index()
    {
        $mentor = auth()->user()->pembimbingDudi;
        
        // Find all students assigned to this mentor's company
        $jurnals = Jurnal::whereHas('siswa', function($q) use ($mentor) {
                $q->where('dudi_id', $mentor->dudi_id);
            })
            ->with(['siswa', 'kompetensi', 'tujuanPembelajaran'])
            ->latest('tanggal')
            ->paginate(15);

        return view('pembimbing-dudi.jurnal.index', compact('jurnals'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        if ($jurnal->status !== 'pending') {
            $request->validate([
                'catatan_pembimbing' => 'nullable|string'
            ]);
            $jurnal->update([
                'catatan_pembimbing' => $request->catatan_pembimbing
            ]);

            if ($request->filled('catatan_pembimbing')) {
                \App\Models\Notifikasi::create([
                    'from_user_id' => auth()->id(),
                    'to_user_id' => $jurnal->siswa->user_id,
                    'judul' => 'Saran Jurnal Baru',
                    'pesan' => 'Pembimbing DUDI memberikan saran/komentar pada jurnal Anda tanggal ' . \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('D MMMM YYYY') . '.',
                    'tipe' => 'jurnal_comment',
                    'is_read' => 0
                ]);
            }

            return back()->with('success', 'Saran / Komentar berhasil dikirim.');
        }

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
