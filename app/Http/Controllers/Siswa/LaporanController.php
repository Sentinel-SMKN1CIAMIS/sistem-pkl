<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            'file' => 'required|file|mimes:pdf,docx,doc|max:5120',
        ]);

        $siswa = auth()->user()->siswa;
        
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('laporan', 'public');
            
            LaporanPkl::updateOrCreate(
                ['siswa_id' => $siswa->id],
                [
                    'judul' => $request->judul,
                    'deskripsi' => $request->deskripsi,
                    'file_path' => $path,
                    'status' => 'submitted',
                    'submitted_at' => Carbon::now()
                ]
            );
        }

        return redirect()->route('siswa.laporan.index')->with('success', 'Laporan berhasil diunggah.');
    }
}
