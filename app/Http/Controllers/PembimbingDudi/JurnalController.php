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
        
        // Find all students assigned to this mentor
        $jurnals = Jurnal::whereHas('siswa', function($q) use ($mentor) {
                $q->where('pembimbing_dudi_id', $mentor->id);
            })
            ->with(['siswa', 'kompetensi', 'tujuanPembelajaran'])
            ->orderByRaw("CASE WHEN (COALESCE(approval_status, 'pending') = 'pending' AND COALESCE(status, 'pending') = 'pending') THEN 0 ELSE 1 END")
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembimbing-dudi.jurnal.index', compact('jurnals'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        // [SECURITY FIX CRIT-04] Verifikasi bahwa jurnal ini milik siswa di DUDI mentor ini
        $mentor = auth()->user()->pembimbingDudi;
        if (!$mentor || $jurnal->siswa->pembimbing_dudi_id !== $mentor->id) {
            abort(403, 'Anda tidak memiliki wewenang untuk memodifikasi jurnal siswa ini.');
        }

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

    /**
     * Bulk approve (ACC) multiple pending journals belonging to this DUDI mentor's students.
     */
    public function bulkApprove(Request $request)
    {
        $mentor = auth()->user()->pembimbingDudi;
        if (!$mentor) {
            abort(403, 'Akun Anda bukan Pembimbing DUDI.');
        }

        $validated = $request->validate([
            'jurnal_ids' => ['required', 'array', 'min:1'],
            'jurnal_ids.*' => ['integer', 'exists:jurnals,id'],
            'catatan_pembimbing' => ['nullable', 'string', 'max:500'],
        ], [
            'jurnal_ids.required' => 'Pilih minimal 1 jurnal untuk disetujui.',
        ]);

        // Only approve pending journals strictly owned by students assigned to this DUDI mentor
        $jurnals = Jurnal::with('siswa')
            ->whereIn('id', $validated['jurnal_ids'])
            ->where('status', 'pending')
            ->whereHas('siswa', fn($q) => $q->where('pembimbing_dudi_id', $mentor->id))
            ->get();

        if ($jurnals->isEmpty()) {
            return back()->with('error', 'Tidak ada jurnal pending yang valid untuk disetujui.');
        }

        $count = 0;
        $note = $validated['catatan_pembimbing'] ?? null;

        foreach ($jurnals as $jurnal) {
            $jurnal->update([
                'status' => 'valid',
                'catatan_pembimbing' => $note ?: $jurnal->catatan_pembimbing,
            ]);

            if ($jurnal->siswa && $jurnal->siswa->user_id) {
                \App\Models\Notifikasi::create([
                    'from_user_id' => auth()->id(),
                    'to_user_id' => $jurnal->siswa->user_id,
                    'judul' => 'Jurnal Divalidasi',
                    'pesan' => 'Jurnal kegiatan Anda pada tanggal ' . $jurnal->tanggal . ' telah disetujui oleh pembimbing DUDI.',
                    'tipe' => 'success',
                    'is_read' => false,
                ]);
            }

            $count++;
        }

        return back()->with('success', "{$count} jurnal siswa berhasil disetujui (ACC) sekaligus.");
    }
}
