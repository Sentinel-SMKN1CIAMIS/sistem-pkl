<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurnal;
use App\Models\Kompetensi;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    private function requirePkl()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa || !$siswa->dudi_id) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum memiliki tempat PKL yang disetujui. Silakan ajukan terlebih dahulu.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $jurnals = Jurnal::where('siswa_id', $siswa->id)
            ->with(['kompetensi'])
            ->latest('tanggal')
            ->paginate(10);
            
        return view('siswa.jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $today = \Carbon\Carbon::today();

        // Cek apakah siswa sudah absen hari ini
        $hasAbsensiToday = \App\Models\Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->exists();

        // Cek apakah siswa sudah mengisi jurnal hari ini
        $hasJurnalToday = Jurnal::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->exists();

        if ($hasJurnalToday) {
            return redirect()->route('siswa.jurnal.index')->with('error', 'Anda sudah mengisi jurnal untuk hari ini. Anda hanya dapat mengisi 1 jurnal per hari.');
        }

        if (!$hasAbsensiToday) {
            return redirect()->route('siswa.absensi.index')->with('error', 'Anda harus mengisi daftar hadir (Absen Datang) hari ini sebelum dapat mengisi jurnal.');
        }

        $kompetensis = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)->get();
        return view('siswa.jurnal.create', compact('kompetensis'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'kompetensi_id' => 'required|exists:kompetensis,id',
            'cp'            => 'nullable|string|max:500',
            'tanggal'       => 'required|date',
            'kegiatan'      => 'required|string',
            'catatan'       => 'nullable|string',
            'foto_cropped'  => 'nullable|string', // Base64 cropped image
        ]);

        $siswa = auth()->user()->siswa;

        // Validasi apakah siswa memiliki absen pada tanggal jurnal yang diinput
        $hasAbsensiForDate = \App\Models\Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if (!$hasAbsensiForDate) {
            return back()->withInput()->with('error', 'Anda belum mengisi daftar hadir pada tanggal ' . \Carbon\Carbon::parse($request->tanggal)->format('d/m/Y') . '. Silakan isi absensi terlebih dahulu sebelum mengisi jurnal.');
        }

        // Validasi apakah siswa sudah memiliki jurnal pada tanggal tersebut
        $hasJurnalForDate = Jurnal::where('siswa_id', $siswa->id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($hasJurnalForDate) {
            return back()->withInput()->with('error', 'Anda sudah mengisi jurnal pada tanggal ' . \Carbon\Carbon::parse($request->tanggal)->format('d/m/Y') . '. Satu hari hanya boleh 1 jurnal.');
        }

        $data = [
            'siswa_id'           => $siswa->id,
            'kompetensi_id'      => $request->kompetensi_id,
            'cp'                 => $request->cp,
            'tanggal'            => $request->tanggal,
            'deskripsi_pekerjaan'=> $request->kegiatan,
            'catatan'            => $request->catatan,
            'status'             => 'pending',
        ];

        // Handle cropped base64 image
        if ($request->filled('foto_cropped')) {
            $imageData = $request->foto_cropped;
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'jurnal/' . $siswa->id . '_' . time() . '.png';
            Storage::disk('public')->put($fileName, base64_decode($imageData));
            $data['foto_path'] = $fileName;
        }

        $jurnal = Jurnal::create($data);

        // Notify Mentor Industri
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
