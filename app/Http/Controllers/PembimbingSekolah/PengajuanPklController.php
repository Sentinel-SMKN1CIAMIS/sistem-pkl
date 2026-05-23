<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Models\Dudi;
use Illuminate\Http\Request;

class PengajuanPklController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        // Get classes the teacher teaches
        $kelasIds = $teacher->kelasDiajar()->pluck('kelas')->toArray();

        // Get pengajuan from students in those classes
        $pengajuans = PengajuanPkl::whereHas('siswa', function ($query) use ($kelasIds) {
            $query->whereIn('kelas', $kelasIds);
        })->with('siswa')->latest()->paginate(10);

        return view('pembimbing-sekolah.pengajuan-pkl.index', compact('pengajuans'));
    }

    public function update(Request $request, PengajuanPkl $pengajuanPkl)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $teacher = auth()->user()->pembimbingSekolah;

        $pengajuanPkl->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
            'acc_oleh' => auth()->id()
        ]);

        if ($request->status === 'disetujui') {
            // Find or create DUDI
            $dudi = Dudi::firstOrCreate(
                ['nama' => $pengajuanPkl->nama_perusahaan],
                [
                    'pimpinan' => $pengajuanPkl->pimpinan,
                    'alamat' => $pengajuanPkl->alamat,
                    'no_telp' => $pengajuanPkl->no_telp,
                ]
            );

            // Assign DUDI to Siswa
            $pengajuanPkl->siswa->update(['dudi_id' => $dudi->id]);
        }

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
