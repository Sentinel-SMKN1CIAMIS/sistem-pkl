<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LaporanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;
        $laporan = LaporanPkl::where('siswa_id', $siswa->id)->first();
        return view('siswa.laporan.index', compact('laporan'));
    }

    public function store(Request $request)
    {
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
