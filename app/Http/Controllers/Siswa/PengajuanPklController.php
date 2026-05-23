<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use Illuminate\Http\Request;

class PengajuanPklController extends Controller
{
    public function create()
    {
        $siswa = auth()->user()->siswa;

        // Jika sudah punya DUDI, tidak perlu mengajukan lagi
        if ($siswa->dudi_id) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah memiliki tempat PKL yang telah disetujui.');
        }

        // Jika sudah ada pengajuan yang menunggu atau disetujui
        $existing = $siswa->pengajuanPkl;
        if ($existing && in_array($existing->status, ['menunggu', 'disetujui'])) {
            return redirect()->route('siswa.pengajuan_pkl.status');
        }

        return view('siswa.pengajuan-pkl.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'pimpinan'        => 'nullable|string|max:255',
            'alamat'          => 'nullable|string|max:1000',
            'no_telp'         => 'nullable|string|max:30',
        ]);

        $siswa = auth()->user()->siswa;

        if ($siswa->dudi_id) {
            return redirect()->route('dashboard');
        }

        // Hapus pengajuan ditolak sebelumnya dan buat yang baru
        $siswa->pengajuanPkl()->delete();

        PengajuanPkl::create([
            'siswa_id'        => $siswa->id,
            'nama_perusahaan' => $request->nama_perusahaan,
            'pimpinan'        => $request->pimpinan,
            'alamat'          => $request->alamat,
            'no_telp'         => $request->no_telp,
            'status'          => 'menunggu',
        ]);

        return redirect()->route('siswa.pengajuan_pkl.status')
            ->with('success', 'Pengajuan tempat PKL berhasil dikirim! Mohon tunggu konfirmasi dari Guru Pembimbing.');
    }

    public function status()
    {
        $siswa     = auth()->user()->siswa;
        $pengajuan = $siswa->pengajuanPkl;

        return view('siswa.pengajuan-pkl.status', compact('pengajuan'));
    }
}
