<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use App\Models\LaporanPkl;
use App\Models\Siswa;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $pembimbing = auth()->user()->pembimbingSekolah;
        
        $laporans = LaporanPkl::with('siswa')
            ->whereHas('siswa', function ($query) use ($pembimbing) {
                $query->where('pembimbing_sekolah_id', $pembimbing->id);
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('pembimbing-sekolah.laporan.index', compact('laporans'));
    }

    public function update(Request $request, LaporanPkl $laporan)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // Ensure the laporan belongs to a student guided by this teacher
        $pembimbing = auth()->user()->pembimbingSekolah;
        if ($laporan->siswa->pembimbing_sekolah_id !== $pembimbing->id) {
            abort(403);
        }

        $laporan->update([
            'status' => $request->status
        ]);

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        return redirect()->route('pembimbing_sekolah.laporan.index')->with('success', "Laporan berhasil $statusText.");
    }
}
