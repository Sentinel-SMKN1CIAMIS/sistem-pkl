<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;
use App\Models\Kompetensi;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;
        $jurnals = Jurnal::where('siswa_id', $siswa->id)
            ->with(['kompetensi'])
            ->latest('tanggal')
            ->paginate(10);
            
        return view('siswa.jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        $siswa = auth()->user()->siswa;
        $kompetensis = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)->get();
        return view('siswa.jurnal.create', compact('kompetensis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kompetensi_id' => 'required|exists:kompetensis,id',
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string',
            'catatan' => 'nullable|string',
            'foto' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('foto');
        $data['siswa_id'] = auth()->user()->siswa->id;
        $data['status'] = 'pending';

        if ($request->hasFile('foto')) {
            $data['foto_path'] = $request->file('foto')->store('jurnal', 'public');
        }

        $jurnal = Jurnal::create($data);

        // Notify Mentor Industri
        $siswa = auth()->user()->siswa;
        if($siswa->pembimbing_dudi_id) {
            \App\Models\Notifikasi::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $siswa->pembimbingDudi->user_id,
                'judul' => 'Jurnal Baru dari ' . $siswa->nama_lengkap,
                'pesan' => 'Siswa Anda telah mengirimkan jurnal harian baru untuk tanggal ' . $data['tanggal'],
                'tipe' => 'info'
            ]);
        }

        return redirect()->route('siswa.jurnal.index')->with('success', 'Jurnal berhasil disimpan.');
    }

    public function destroy(Jurnal $jurnal)
    {
        if ($jurnal->siswa_id !== auth()->user()->siswa->id) { abort(403); }
        if ($jurnal->status !== 'pending') { 
            return back()->with('error', 'Jurnal yang sudah diproses tidak dapat dihapus.');
        }

        if ($jurnal->foto_path) {
            Storage::disk('public')->delete($jurnal->foto_path);
        }

        $jurnal->delete();
        return redirect()->route('siswa.jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
    }
}
