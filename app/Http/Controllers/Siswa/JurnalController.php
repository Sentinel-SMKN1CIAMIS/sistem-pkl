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
                ->with('error', 'Anda belum dapat mengakses menu ini. Pastikan pengajuan PKL telah disetujui.');
        }

        if ($siswa->status_pkl === 'belum_mulai') {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Tempat PKL Anda sudah disetujui, namun Anda belum bisa mengakses menu ini karena menunggu Tim Pokja memetakan Guru Pembimbing Sekolah.');
        }

        if (!in_array($siswa->status_pkl, ['sedang_pkl', 'selesai'])) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum dapat mengakses menu ini. Pastikan Surat Pengantar telah di-ACC dan DUDI telah membalas (menerima) Anda.');
        }

        return null;
    }

    public function checkAttendance(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return response()->json(['exists' => false, 'status' => null]);
        }

        $absensi = \App\Models\Absensi::where('siswa_id' . '', $siswa->id)
            ->where('tanggal' . '', $request->tanggal)
            ->first();

        if ($absensi) {
            return response()->json([
                'exists' => true,
                'status' => $absensi->status,
                'waktu_datang' => $absensi->waktu_datang ? \Carbon\Carbon::parse($absensi->waktu_datang)->format('H:i') : null,
                'waktu_pulang' => $absensi->waktu_pulang ? \Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') : null,
                'keterangan' => $absensi->keterangan,
                'alasan' => $absensi->alasan,
            ]);
        }

        return response()->json([
            'exists' => false,
            'status' => null,
        ]);
    }

    public function index()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $jurnals = Jurnal::where('siswa_id', $siswa->id)
            ->with(['kompetensi', 'tujuanPembelajaran'])
            ->latest('tanggal')
            ->paginate(10);
            
        $hasAbsenToday = \App\Models\Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', \Carbon\Carbon::today()->toDateString())
            ->exists();
            
        return view('siswa.jurnal.index', compact('jurnals', 'hasAbsenToday'));
    }

    public function create()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $today = \Carbon\Carbon::today();

        // Check removed: Students can open the form any day to backfill journals.

        $kompetensis = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)->get();
        
        // Get CP/TP master data for dropdown (Tujuan Pembelajaran)
        $tujuanPembelajaran = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)
            ->whereNotNull('tp')
            ->get();
        
        // Get max backdate allowed
        $maxBackdateDays = Jurnal::getMaxBackdateDays();
        $minDate = $today->copy()->subDays($maxBackdateDays)->format('Y-m-d');
        $maxDate = $today->format('Y-m-d');
        
        return view('siswa.jurnal.create', compact('kompetensis', 'tujuanPembelajaran', 'minDate', 'maxDate'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'kompetensi_id' => 'required|exists:kompetensis,id',
            'cp_id'         => 'nullable|exists:kompetensis,id',
            'cp'            => 'nullable|string|max:500',
            'tanggal'       => 'required|date',
            'kegiatan'      => 'required|string',
            'catatan'       => 'nullable|string',
            'foto_cropped'  => 'nullable|string', // Base64 cropped image
        ]);

        $siswa = auth()->user()->siswa;
        $requestDate = \Carbon\Carbon::parse($request->tanggal);

        // Validasi backdate - cek apakah tanggal dalam jangkauan yang diperbolehkan (max 7 hari lalu)
        if (!Jurnal::isDateAllowedForEntry($request->tanggal)) {
            $maxBackdateDays = Jurnal::getMaxBackdateDays();
            return back()->withInput()->with('error', 'Anda hanya dapat mengisi jurnal untuk maksimal ' . $maxBackdateDays . ' hari sebelumnya.');
        }

        // Validasi apakah siswa sudah memiliki jurnal pada tanggal tersebut
        $hasJurnalForDate = Jurnal::where('siswa_id', $siswa->id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($hasJurnalForDate) {
            return back()->withInput()->with('error', 'Anda sudah mengisi jurnal pada tanggal ' . $requestDate->format('d/m/Y') . '. Satu hari hanya boleh 1 jurnal.');
        }

        // Cek status absensi untuk tanggal yang diinput
        $absensi = \App\Models\Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        // Block journal creation if status is izin or sakit
        if ($absensi && in_array($absensi->status, ['izin', 'sakit'])) {
            return back()->withInput()->with('error', 'Anda tidak dapat mengisi jurnal pada hari di mana Anda sedang Izin atau Sakit.');
        }

        if (!$absensi) {
            $request->validate([
                'alasan_alpha' => 'required|string|min:10|max:500',
            ], [
                'alasan_alpha.required' => 'Anda wajib mengisi alasan tidak melakukan absensi pada tanggal ini.',
                'alasan_alpha.min' => 'Alasan tidak melakukan absensi minimal 10 karakter.',
            ]);

            \App\Models\Absensi::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $request->tanggal,
                'status' => 'alpha',
                'alasan' => $request->alasan_alpha,
                'approval_status' => 'pending',
            ]);
        } elseif ($absensi->status === 'alpha' && $request->filled('alasan_alpha')) {
            $absensi->update([
                'alasan' => $request->alasan_alpha,
            ]);
        }

        $data = [
            'siswa_id'           => $siswa->id,
            'kompetensi_id'      => $request->kompetensi_id,
            'cp_id'              => $request->cp_id,
            'cp'                 => $request->cp,
            'tanggal'            => $request->tanggal,
            'deskripsi_pekerjaan'=> $request->kegiatan,
            'catatan'            => $request->catatan,
            'status'             => 'pending',
        ];

        // Handle cropped base64 image
        if ($request->filled('foto_cropped')) {
            $imageData = $request->foto_cropped;
            if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $imageData)) {
                return back()->withInput()->with('error', 'Format foto tidak valid. Harus berupa gambar JPEG atau PNG.');
            }
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $binaryData = base64_decode($imageData);

            // Compress to JPEG 85% using GD if available
            if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
                $image = @imagecreatefromstring($binaryData);
                if ($image !== false) {
                    ob_start();
                    imagejpeg($image, null, 85);
                    $binaryData = ob_get_clean();
                    imagedestroy($image);
                }
            }

            $fileName = 'jurnal/' . $siswa->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($fileName, $binaryData);
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

    public function edit(Jurnal $jurnal)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;

        if ($jurnal->siswa_id !== $siswa->id) { abort(403); }
        if ($jurnal->status !== 'pending') {
            return redirect()->route('siswa.jurnal.index')->with('error', 'Jurnal yang sudah diproses tidak dapat diedit.');
        }

        $kompetensis = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)->get();
        $tujuanPembelajaran = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)
            ->whereNotNull('tp')
            ->get();

        $today = \Carbon\Carbon::today();
        $maxBackdateDays = Jurnal::getMaxBackdateDays();
        $minDate = $today->copy()->subDays($maxBackdateDays)->format('Y-m-d');
        $maxDate = $today->format('Y-m-d');

        return view('siswa.jurnal.edit', compact('jurnal', 'kompetensis', 'tujuanPembelajaran', 'minDate', 'maxDate'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;

        if ($jurnal->siswa_id !== $siswa->id) { abort(403); }
        if ($jurnal->status !== 'pending') {
            return redirect()->route('siswa.jurnal.index')->with('error', 'Jurnal yang sudah diproses tidak dapat diedit.');
        }

        $request->validate([
            'kompetensi_id' => 'required|exists:kompetensis,id',
            'cp_id'         => 'nullable|exists:kompetensis,id',
            'cp'            => 'nullable|string|max:500',
            'tanggal'       => 'required|date',
            'kegiatan'      => 'required|string',
            'catatan'       => 'nullable|string',
            'foto_cropped'  => 'nullable|string',
        ]);

        $requestDate = \Carbon\Carbon::parse($request->tanggal);

        // Validasi backdate
        if (!Jurnal::isDateAllowedForEntry($request->tanggal)) {
            $maxBackdateDays = Jurnal::getMaxBackdateDays();
            return back()->withInput()->with('error', 'Anda hanya dapat mengisi jurnal untuk maksimal ' . $maxBackdateDays . ' hari sebelumnya.');
        }

        // Cek tanggal duplikat jika tanggal diubah
        if ($jurnal->tanggal !== $request->tanggal) {
            $hasJurnalForDate = Jurnal::where('siswa_id', $siswa->id)
                ->where('tanggal', $request->tanggal)
                ->exists();

            if ($hasJurnalForDate) {
                return back()->withInput()->with('error', 'Anda sudah mengisi jurnal pada tanggal ' . $requestDate->format('d/m/Y') . '. Satu hari hanya boleh 1 jurnal.');
            }
        }

        // Cek status absensi untuk tanggal yang diinput
        $absensi = \App\Models\Absensi::where('siswa_id' . '', $siswa->id)
            ->where('tanggal' . '', $request->tanggal)
            ->first();

        if (!$absensi) {
            $request->validate([
                'alasan_alpha' => 'required|string|min:10|max:500',
            ], [
                'alasan_alpha.required' => 'Anda wajib mengisi alasan tidak melakukan absensi pada tanggal ini.',
                'alasan_alpha.min' => 'Alasan tidak melakukan absensi minimal 10 karakter.',
            ]);

            \App\Models\Absensi::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $request->tanggal,
                'status' => 'alpha',
                'alasan' => $request->alasan_alpha,
                'approval_status' => 'pending',
            ]);
        } elseif ($absensi->status === 'alpha' && $request->filled('alasan_alpha')) {
            $absensi->update([
                'alasan' => $request->alasan_alpha,
            ]);
        }

        $data = [
            'kompetensi_id'      => $request->kompetensi_id,
            'cp_id'              => $request->cp_id,
            'cp'                 => $request->cp,
            'tanggal'            => $request->tanggal,
            'deskripsi_pekerjaan'=> $request->kegiatan,
            'catatan'            => $request->catatan,
        ];

        if ($request->filled('foto_cropped')) {
            $imageData = $request->foto_cropped;
            if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $imageData)) {
                return back()->withInput()->with('error', 'Format foto tidak valid. Harus berupa gambar JPEG atau PNG.');
            }
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $binaryData = base64_decode($imageData);

            // Compress to JPEG 85% using GD if available
            if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
                $image = @imagecreatefromstring($binaryData);
                if ($image !== false) {
                    ob_start();
                    imagejpeg($image, null, 85);
                    $binaryData = ob_get_clean();
                    imagedestroy($image);
                }
            }

            $fileName = 'jurnal/' . $siswa->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($fileName, $binaryData);
            
            if ($jurnal->foto_path) {
                Storage::disk('public')->delete($jurnal->foto_path);
            }
            $data['foto_path'] = $fileName;
        }

        $jurnal->update($data);

        return redirect()->route('siswa.jurnal.index')->with('success', 'Jurnal berhasil diperbarui.');
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
