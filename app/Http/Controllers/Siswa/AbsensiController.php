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

        // Deteksi maksimal 3 hari ke belakang (H-1 s/d H-7) yang belum ada absensi & bukan hari libur rutin
        $maxBackdateDays = \App\Models\Jurnal::getMaxBackdateDays();
        $recentDates = collect();
        $windowStart = $today->copy()->subDays($maxBackdateDays);
        $existingDates = Absensi::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$windowStart->toDateString(), $today->copy()->subDay()->toDateString()])
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $hasStartedPkl = Absensi::where('siswa_id', $siswa->id)->exists();
        if ($hasStartedPkl) {
            for ($offset = 1; $offset <= min($maxBackdateDays, 7); $offset++) {
                $checkDate = $today->copy()->subDays($offset);
                $dateStr = $checkDate->toDateString();
                if ($existingDates->has($dateStr) || $siswa->isRoutineDayOff($checkDate) || \App\Models\HariLiburNasional::findByDate($checkDate) !== null) {
                    continue;
                }
                $recentDates->push([
                    'date' => $dateStr,
                    'label' => $checkDate->isoFormat('dddd, D MMMM YYYY'),
                    'days_ago' => $offset,
                ]);
                if ($recentDates->count() >= 3) {
                    break;
                }
            }
        }

        $missingRecentDates = $recentDates;

        return view('siswa.absensi.index', compact('absensiToday', 'history', 'missingRecentDates'));
    }

    public function clockIn(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'signature' => 'required', // Base64 signature
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required' => 'Lokasi GPS wajib diaktifkan untuk melakukan absensi.',
            'longitude.required' => 'Lokasi GPS wajib diaktifkan untuk melakukan absensi.',
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

        // [SECURITY FIX MED-03] Validasi konten binary signature sebelum disimpan
        $binaryData = base64_decode($signature, true);
        if ($binaryData === false || strlen($binaryData) < 8) {
            return back()->with('error', 'Data tanda tangan tidak valid.');
        }
        // Validasi magic bytes PNG (\x89PNG\r\n\x1a\n)
        if (substr($binaryData, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return back()->with('error', 'Format tanda tangan harus berupa gambar PNG.');
        }

        $fileName = 'signatures/in_' . $siswa->id . '_' . time() . '.png';
        Storage::disk('public')->put($fileName, $binaryData);

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

        // Auto-update status PKL to 'sedang_pkl' on first attendance
        if ($siswa->status_pkl === 'belum_mulai') {
            $siswa->update(['status_pkl' => 'sedang_pkl']);
        }

        return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Masuk.');
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
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if ($absensi->waktu_pulang) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // Check if at least 1 hour has passed since clock-in
        $clockInTime = Carbon::parse($absensi->tanggal . ' ' . $absensi->waktu_datang);
        $now = Carbon::now();
        $oneHourLater = $clockInTime->copy()->addHour();

        if ($now < $oneHourLater) {
            return back()->with('error', 'Anda belum bisa melakukan absen pulang. Minimal 1 jam setelah absen masuk.');
        }

        // Check if 7 hours have passed OR early leave approved
        $sevenHoursLater = $clockInTime->copy()->addHours(7);

        if ($now < $sevenHoursLater) {
            // Less than 7 hours - MUST have approval
            if ($absensi->early_leave_request_status !== 'approved') {
                return back()->with('error', 'Anda belum bisa pulang sebelum 7 jam. Silakan ajukan izin pulang cepat ke pembimbing DUDI terlebih dahulu.');
            }

            // Approved - can clock out early
            $absensi->update([
                'waktu_pulang' => Carbon::now()->toTimeString(),
            ]);

            return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Pulang (Izin disetujui).');
        }

        // Normal clock out (>= 7 hours)
        $absensi->update([
            'waktu_pulang' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Pulang.');
    }

    // T5.3: Submit absence request (izin, sakit, libur) — mendukung hari ini atau susulan (backdate maks H-7)
    public function submitAbsenceRequest(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $maxBackdateDays = \App\Models\Jurnal::getMaxBackdateDays();
        $today = Carbon::today();
        $minDate = $today->copy()->subDays($maxBackdateDays)->toDateString();

        $request->validate([
            'tanggal' => ['nullable', 'date', 'after_or_equal:' . $minDate, 'before_or_equal:' . $today->toDateString()],
            'status' => 'required|in:izin,sakit,libur',
            'alasan' => 'required|string|min:10',
        ], [
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.after_or_equal' => "Tanggal pengajuan susulan maksimal {$maxBackdateDays} hari ke belakang.",
            'tanggal.before_or_equal' => 'Tanggal pengajuan tidak boleh melebihi hari ini.',
            'status.required' => 'Silakan pilih status ketidakhadiran.',
            'status.in' => 'Status ketidakhadiran tidak valid.',
            'alasan.required' => $request->input('status') === 'libur'
                ? 'Alasan libur wajib diisi (misal: Libur DUDI / Tanggal Merah / Off Shift).'
                : 'Alasan ketidakhadiran wajib diisi.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
        ]);

        $siswa = auth()->user()->siswa;
        $tanggal = $request->filled('tanggal') ? Carbon::parse($request->tanggal)->startOfDay() : $today;

        // Check if already submitted for target date
        $exists = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah memiliki catatan absensi untuk tanggal ' . $tanggal->isoFormat('D MMMM YYYY') . '.');
        }

        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $tanggal->toDateString(),
            'status' => $request->status,
            'alasan' => $request->alasan,
            'approval_status' => 'pending',
        ]);

        $dateLabel = $tanggal->isSameDay($today) ? 'hari ini' : 'tanggal ' . $tanggal->isoFormat('D MMMM YYYY');

        return back()->with('success', 'Berhasil mengajukan keterangan ' . $request->status . ' untuk ' . $dateLabel . '. Menunggu persetujuan guru pembimbing.');
    }

    // Request early leave (pulang cepat) - requires approval from Pembimbing DUDI
    public function requestEarlyLeave(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'early_leave_reason' => 'required|string|min:10',
        ], [
            'early_leave_reason.required' => 'Alasan izin pulang cepat wajib diisi.',
            'early_leave_reason.min' => 'Alasan minimal 10 karakter.',
        ]);

        $siswa = auth()->user()->siswa;
        $today = Carbon::today();

        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        if ($absensi->early_leave_request_status === 'pending') {
            return back()->with('error', 'Anda sudah mengajukan izin pulang cepat. Menunggu persetujuan pembimbing DUDI.');
        }

        $absensi->update([
            'early_leave_request_status' => 'pending',
            'early_leave_reason' => $request->early_leave_reason,
            'early_leave_requested_at' => now(),
        ]);

        // Create notification for Pembimbing DUDI
        $pembimbingDudi = $siswa->pembimbingDudi;
        if (!$pembimbingDudi && $siswa->dudi) {
            $pembimbingDudi = $siswa->dudi->pembimbingDudi()->first();
        }
        
        if ($pembimbingDudi && $pembimbingDudi->user_id) {
            \App\Models\Notifikasi::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $pembimbingDudi->user_id,
                'judul' => 'Permintaan Izin Pulang Cepat',
                'pesan' => $siswa->nama_lengkap . ' mengajukan izin pulang cepat pada tanggal ' . Carbon::today()->isoFormat('D MMMM YYYY') . '. Alasan: ' . $request->early_leave_reason,
                'tipe' => 'early_leave_request',
                'link' => route('pembimbing_dudi.absensi.index'),
                'is_read' => 0,
            ]);
        }

        return back()->with('success', 'Permintaan izin pulang cepat telah diajukan. Menunggu persetujuan pembimbing DUDI.');
    }
}
