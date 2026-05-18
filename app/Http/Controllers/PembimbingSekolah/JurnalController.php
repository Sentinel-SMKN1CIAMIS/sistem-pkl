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
            'pesan' => 'Jurnal kegiatan Anda pada tanggal ' . $jurnal->tanggal . ' telah ' . ($request->status == 'valid' ? 'disetujui' : 'ditolak') . ' oleh pembimbing sekolah.',
            'tipe' => $request->status == 'valid' ? 'success' : 'warning'
        ]);

        return back()->with('success', 'Status jurnal berhasil diperbarui.');
    }
    public function validasiSemua(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        // Find all pending jurnals of students assigned to this teacher
        $jurnals = Jurnal::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
            })
            ->where('status', 'pending')
            ->get();

        if ($jurnals->isEmpty()) {
            return back()->with('info', 'Tidak ada jurnal pending yang perlu divalidasi.');
        }

        foreach ($jurnals as $jurnal) {
            $jurnal->update([
                'status' => 'valid'
            ]);

            // Notify Student
            \App\Models\Notifikasi::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $jurnal->siswa->user_id,
                'judul' => 'Jurnal Divalidasi',
                'pesan' => 'Jurnal kegiatan Anda pada tanggal ' . $jurnal->tanggal . ' telah disetujui secara otomatis oleh pembimbing sekolah.',
                'tipe' => 'success'
            ]);
        }

        return back()->with('success', $jurnals->count() . ' jurnal berhasil divalidasi sekaligus.');
    }
}
