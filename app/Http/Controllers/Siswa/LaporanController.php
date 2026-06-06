<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LaporanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaporanController extends Controller
{
    private function requirePkl()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa || !$siswa->dudi_id || !in_array($siswa->status_pkl, ['sedang_pkl', 'selesai'])) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum dapat mengakses menu ini. Pastikan Surat Pengantar telah di-ACC dan DUDI telah membalas (menerima) Anda.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $laporan = LaporanPkl::where('siswa_id', $siswa->id)->first();
        return view('siswa.laporan.index', compact('laporan'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link_media_sosial' => 'nullable|array|max:5',
            'link_media_sosial.*' => 'nullable|url',
        ]);

        $siswa = auth()->user()->siswa;
        
        // Filter empty links
        $links = $request->link_media_sosial ? array_filter($request->link_media_sosial) : null;
        
        LaporanPkl::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'link_media_sosial' => empty($links) ? null : array_values($links),
                'status' => 'submitted',
                'submitted_at' => Carbon::now()
            ]
        );

        return redirect()->route('siswa.laporan.index')->with('success', 'Laporan berhasil diunggah.');
    }
}
