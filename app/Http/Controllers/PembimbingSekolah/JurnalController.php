<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;


class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        $tipe    = $teacher->tipe; // 'produktif', 'normatif', or 'adaptif'

        $query = Jurnal::with(['siswa', 'kompetensi'])
            ->latest('tanggal');

        if ($tipe === 'produktif') {
            // Produktif: tampilkan siswa yang langsung dibimbing atau dari kelas yang diajar
            $kelasIds = $teacher->kelasDiajar()->pluck('kelas')->toArray();
            $query->whereHas('siswa', function ($q) use ($teacher, $kelasIds) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhereIn('kelas', $kelasIds);
            });
        } else {
            // Normatif / Adaptif: filter berdasarkan CP yang mengandung mapel_cp guru
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

        return back()->with('success', 'Saran / Komentar berhasil disimpan.');
    }
}
