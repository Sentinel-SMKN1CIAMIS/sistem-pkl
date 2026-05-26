<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Models\Dudi;
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

        // Ambil daftar DUDI yang relevan dengan konsentrasi keahlian siswa
        $dudis = Dudi::where('is_active', true)
            ->where(function ($q) use ($siswa) {
                $q->where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($siswa) {
                      $sub->where('konsentrasi_keahlians.id', $siswa->konsentrasi_keahlian_id);
                  });
            })->get();

        return view('siswa.pengajuan-pkl.create', compact('dudis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dudi_id'            => 'nullable|exists:dudis,id',
            'pembimbing_dudi_id' => 'nullable|exists:pembimbing_dudis,id',
            'nama_perusahaan'    => 'required|string|max:255',
            'pimpinan'           => 'nullable|string|max:255',
            'alamat'             => 'nullable|string|max:1000',
            'kota'               => 'required_without:dudi_id|nullable|string|max:100',
            'no_telp'            => 'nullable|string|max:30',
        ]);

        $siswa = auth()->user()->siswa;

        if ($siswa->dudi_id) {
            return redirect()->route('dashboard');
        }

        // Hapus pengajuan ditolak sebelumnya dan buat yang baru
        $siswa->pengajuanPkl()->delete();

        PengajuanPkl::create([
            'siswa_id'           => $siswa->id,
            'dudi_id'            => $request->dudi_id,
            'pembimbing_dudi_id' => $request->pembimbing_dudi_id,
            'nama_perusahaan'    => $request->nama_perusahaan,
            'pimpinan'           => $request->pimpinan,
            'alamat'             => $request->alamat,
            'kota'               => $request->kota,
            'no_telp'            => $request->no_telp,
            'status'             => 'menunggu',
        ]);

        return redirect()->route('siswa.pengajuan_pkl.status')
            ->with('success', 'Pengajuan tempat PKL berhasil dikirim! Mohon tunggu konfirmasi dari Guru Pembimbing.');
    }

    public function getPembimbing(Request $request)
    {
        $dudi_id = $request->get('dudi_id');
        if (!$dudi_id) {
            return response()->json([]);
        }
        $pembimbing = \App\Models\PembimbingDudi::where('dudi_id', $dudi_id)->get();
        return response()->json($pembimbing);
    }

    public function status()
    {
        $siswa     = auth()->user()->siswa;
        $pengajuan = $siswa->pengajuanPkl;

        return view('siswa.pengajuan-pkl.status', compact('pengajuan'));
    }
}
