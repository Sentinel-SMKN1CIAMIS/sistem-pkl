<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;
use App\Models\Notifikasi;


class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        $tipe    = $teacher->tipe; // 'kejuruan' or 'umum' (previously 'produktif', 'normatif', or 'adaptif')

        $query = Jurnal::with(['siswa', 'kompetensi', 'tujuanPembelajaran'])
            ->orderBy('created_at', 'desc');

        if ($tipe === 'kejuruan' || $tipe === 'produktif') {
            // Kejuruan / Produktif: tampilkan siswa yang langsung dibimbing atau dari kelas yang diajar
            $kelasIds = $teacher->kelasDiajar()->pluck('kelas')->toArray();
            $query->whereHas('siswa', function ($q) use ($teacher, $kelasIds) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhereIn('kelas', $kelasIds);
            });
        } elseif ($tipe === 'keduanya') {
            // Keduanya: tampilkan siswa yang langsung dibimbing OR (siswa di kelas yang diajar AND cocok dengan mapel_cp jika diisi)
            $kelasIds = $teacher->kelasDiajar()->pluck('kelas')->toArray();
            $query->where(function ($q) use ($teacher, $kelasIds) {
                $q->whereHas('siswa', function ($sq) use ($teacher) {
                    $sq->where('pembimbing_sekolah_id', $teacher->id);
                });
                
                if (!empty($kelasIds)) {
                    $q->orWhere(function ($oq) use ($teacher, $kelasIds) {
                        $oq->whereHas('siswa', function ($sq) use ($kelasIds) {
                            $sq->whereIn('kelas', $kelasIds);
                        });
                        if ($teacher->mapel_cp) {
                            $oq->where('cp', 'like', '%' . $teacher->mapel_cp . '%');
                        }
                    });
                }
            });
        } else {
            // Umum (Normatif / Adaptif): filter berdasarkan CP yang mengandung mapel_cp guru
            $kelasIds = $teacher->kelasDiajar()->pluck('kelas')->toArray();
            $query->whereHas('siswa', function ($q) use ($kelasIds) {
                    $q->whereIn('kelas', $kelasIds);
                });

            if ($teacher->mapel_cp) {
                $query->where('cp', 'like', '%' . $teacher->mapel_cp . '%');
            }
        }

        // Filter pencarian tambahan (opsional)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cp', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi_pekerjaan', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', fn($s) => $s->where('nama_lengkap', 'like', '%' . $search . '%')
                                                      ->orWhere('nis', 'like', '%' . $search . '%'));
            });
        }

        // Filter berdasarkan status approval
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $jurnals = $query->paginate(15)->withQueryString();

        return view('pembimbing-sekolah.jurnal.index', compact('jurnals', 'teacher', 'tipe'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $request->validate([
            'catatan_guru' => 'nullable|string'
        ]);

        $jurnal->update([
            'catatan_guru' => $request->catatan_guru
        ]);

        if ($request->filled('catatan_guru')) {
            Notifikasi::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $jurnal->siswa->user_id,
                'judul' => 'Saran Jurnal Baru',
                'pesan' => 'Pembimbing Sekolah memberikan saran/komentar pada jurnal Anda tanggal ' . \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('D MMMM YYYY') . '.',
                'tipe' => 'jurnal_comment',
                'is_read' => 0
            ]);
        }

        return back()->with('success', 'Saran / Komentar berhasil dikirim.');
    }

    public function approve(Request $request, Jurnal $jurnal)
    {
        $jurnal->update([
            'status' => 'valid',
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes
        ]);

        // Create notification for student
        Notifikasi::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $jurnal->siswa->user_id,
            'judul' => 'Jurnal Disetujui',
            'pesan' => 'Jurnal Anda pada tanggal ' . \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('D MMMM YYYY') . ' telah disetujui oleh Guru Pembimbing.',
            'tipe' => 'jurnal_approved',
            'is_read' => 0
        ]);

        return back()->with('success', 'Jurnal berhasil disetujui (ACC).');
    }

    public function reject(Request $request, Jurnal $jurnal)
    {
        $request->validate([
            'approval_notes' => 'required|string|min:3'
        ]);

        $jurnal->update([
            'status' => 'invalid',
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes
        ]);

        // Create notification for student
        Notifikasi::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $jurnal->siswa->user_id,
            'judul' => 'Jurnal Ditolak',
            'pesan' => 'Jurnal Anda pada tanggal ' . \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('D MMMM YYYY') . ' ditolak. Catatan: ' . $request->approval_notes,
            'tipe' => 'jurnal_rejected',
            'is_read' => 0
        ]);

        return back()->with('success', 'Jurnal berhasil ditolak dengan catatan.');
    }
}
