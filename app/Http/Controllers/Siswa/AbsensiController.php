<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsensiController extends Controller
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

    public function index()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $today = Carbon::today();
        
        $absensiToday = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        $history = Absensi::where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('siswa.absensi.index', compact('absensiToday', 'history'));
    }

    public function clockIn(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'signature' => 'required', // Base64 signature
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        $siswa = auth()->user()->siswa;
        $today = Carbon::today();

        // Check if already clocked in
        $exists = Absensi::where('siswa_id', $siswa->id)->where('tanggal', $today)->exists();
        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        // Save signature
        $signature = $request->signature;
        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = str_replace(' ', '+', $signature);
        $fileName = 'signatures/in_' . $siswa->id . '_' . time() . '.png';
        Storage::disk('public')->put($fileName, base64_decode($signature));

        // T5.1: Silently capture GPS location without showing to student
        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'approval_status' => 'approved', // Auto-approve clock-in
            'waktu_datang' => Carbon::now()->toTimeString(),
            'ttd_siswa_path' => $fileName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Datang.');
    }

    public function clockOut(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        $today = Carbon::today();

        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum melakukan absen datang hari ini.');
        }

        if ($absensi->waktu_pulang) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // T5.2: Check if 7 hours have passed since clock-in
        $clockInTime = Carbon::parse($absensi->tanggal . ' ' . $absensi->waktu_datang);
        $now = Carbon::now();
        $sevenHoursLater = $clockInTime->copy()->addHours(7);

        if ($now < $sevenHoursLater) {
            $diffInMinutes = $now->diffInMinutes($sevenHoursLater);
            $hours = intdiv($diffInMinutes, 60);
            $minutes = $diffInMinutes % 60;
            return back()->with('error', "Anda baru bisa absen pulang setelah 7 jam sejak absen datang. Sisa waktu: $hours jam $minutes menit.");
        }

        $absensi->update([
            'waktu_pulang' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Pulang.');
    }

    // T5.3: Submit absence request (izin, sakit, alpa)
    public function submitAbsenceRequest(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'status' => 'required|in:izin,sakit,alpa',
            'alasan' => 'required|min:10',
            'tanggal' => 'required|date|before_or_equal:today|after_or_equal:-7 days',
        ], [
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh lebih dari 7 hari yang lalu.',
        ]);

        $siswa = auth()->user()->siswa;
        $tanggal = Carbon::parse($request->tanggal);

        // Check if already submitted for today
        $exists = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $tanggal)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah memiliki absensi untuk tanggal tersebut.');
        }

        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $tanggal,
            'status' => $request->status,
            'alasan' => $request->alasan,
            'approval_status' => 'pending',
        ]);

        return back()->with('success', 'Berhasil mengajukan permintaan ' . $request->status . '. Menunggu persetujuan guru pembimbing.');
    }
}
