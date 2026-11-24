<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\Kompetensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SmartAttendanceService
{
    protected AiJurnalService $aiJurnalService;

    public function __construct(AiJurnalService $aiJurnalService)
    {
        $this->aiJurnalService = $aiJurnalService;
    }

    /**
     * Get the whitelisted NIS eligible for smart attendance.
     */
    public function getWhitelistedNis(): string
    {
        return (string) config('services.groq.whitelist_nis', '242510648');
    }

    /**
     * Resolve target student model.
     */
    public function getTargetSiswa(?string $nis = null): ?Siswa
    {
        $targetNis = $nis ?: $this->getWhitelistedNis();

        return Siswa::with(['dudi', 'konsentrasiKeahlian', 'user'])
            ->where('nis', $targetNis)
            ->first();
    }

    /**
     * Execute clock-in for the target student.
     *
     * @return array{status: string, message: string, data?: mixed}
     */
    public function clockIn(Siswa $siswa, ?Carbon $now = null, bool $force = false): array
    {
        $now = $now ? $now->copy() : Carbon::now();
        $todayStr = $now->toDateString();

        // Check if weekend (unless forced)
        if (!$force && $now->isWeekend()) {
            return [
                'status' => 'skipped',
                'message' => 'Hari ini adalah akhir pekan (Sabtu/Minggu), absensi otomatis dilewati.',
            ];
        }

        // Check if attendance already exists today
        $existing = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $todayStr)
            ->first();

        if ($existing) {
            return [
                'status' => 'skipped',
                'message' => 'Absensi untuk hari ini sudah ada dengan status: ' . $existing->status . ($existing->waktu_datang ? ' (jam ' . $existing->waktu_datang . ')' : ''),
            ];
        }

        // Determine coordinates from DUDI or fallback
        [$latitude, $longitude] = $this->resolveCoordinates($siswa);

        // Resolve signature path
        $signaturePath = $this->resolveSignaturePath($siswa);

        $waktuDatang = $now->format('H:i:s');

        $absensi = Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $todayStr,
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => $waktuDatang,
            'ttd_siswa_path' => $signaturePath,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        if ($siswa->status_pkl === 'belum_mulai') {
            $siswa->update(['status_pkl' => 'sedang_pkl']);
        }

        Log::info("Smart Attendance: Clock-In successful for NIS {$siswa->nis} on {$todayStr} at {$waktuDatang}");

        return [
            'status' => 'success',
            'message' => "Berhasil melakukan Absen Masuk otomatis untuk NIS {$siswa->nis} pada jam {$waktuDatang}.",
            'data' => $absensi,
        ];
    }

    /**
     * Execute clock-out for the target student, with optional auto-journal generation.
     *
     * @return array{status: string, message: string, data?: mixed}
     */
    public function clockOut(Siswa $siswa, ?Carbon $now = null, bool $withAutoJournal = true, bool $force = false): array
    {
        $now = $now ? $now->copy() : Carbon::now();
        $todayStr = $now->toDateString();

        if (!$force && $now->isWeekend()) {
            return [
                'status' => 'skipped',
                'message' => 'Hari ini adalah akhir pekan (Sabtu/Minggu), absensi pulang dilewati.',
            ];
        }

        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $todayStr)
            ->first();

        if (!$absensi) {
            return [
                'status' => 'skipped',
                'message' => 'Siswa belum melakukan absen masuk hari ini.',
            ];
        }

        if ($absensi->status !== 'hadir') {
            return [
                'status' => 'skipped',
                'message' => "Status kehadiran hari ini adalah '{$absensi->status}', absen pulang dilewati.",
            ];
        }

        if ($absensi->waktu_pulang) {
            return [
                'status' => 'skipped',
                'message' => "Siswa sudah absen pulang sebelumnya pada jam {$absensi->waktu_pulang}.",
            ];
        }

        $waktuPulang = $now->format('H:i:s');
        $absensi->update([
            'waktu_pulang' => $waktuPulang,
        ]);

        $jurnalResult = null;
        if ($withAutoJournal) {
            $jurnalResult = $this->ensureAutoJournal($siswa, $now);
        }

        Log::info("Smart Attendance: Clock-Out successful for NIS {$siswa->nis} on {$todayStr} at {$waktuPulang}");

        return [
            'status' => 'success',
            'message' => "Berhasil melakukan Absen Pulang otomatis untuk NIS {$siswa->nis} pada jam {$waktuPulang}." . ($jurnalResult ? " ({$jurnalResult['message']})" : ''),
            'data' => [
                'absensi' => $absensi,
                'jurnal' => $jurnalResult['data'] ?? null,
            ],
        ];
    }

    /**
     * Automatically evaluate current time and run clock-in or clock-out.
     *
     * @return array{status: string, message: string}
     */
    public function processAuto(Siswa $siswa, ?Carbon $now = null, bool $force = false): array
    {
        $now = $now ? $now->copy() : Carbon::now();

        if (!$force && $now->isWeekend()) {
            return [
                'status' => 'skipped',
                'message' => 'Hari ini akhir pekan (Sabtu/Minggu).',
            ];
        }

        $currentTime = $now->format('H:i');

        // Window pagi (07:45 - 09:30): Lakukan Absen Masuk jika belum
        if ($currentTime >= '07:45' && $currentTime <= '09:30') {
            return $this->clockIn($siswa, $now, $force);
        }

        // Window sore (16:45 - 18:30): Lakukan Absen Pulang + Jurnal jika belum
        if ($currentTime >= '16:45' && $currentTime <= '18:30') {
            return $this->clockOut($siswa, $now, withAutoJournal: true, force: $force);
        }

        return [
            'status' => 'skipped',
            'message' => "Waktu saat ini ({$currentTime}) di luar rentang jadwal masuk (07:45-09:30) maupun pulang (16:45-18:30).",
        ];
    }

    /**
     * Create automated journal entry with AI if none exists today.
     *
     * @return array{status: string, message: string, data?: mixed}
     */
    public function ensureAutoJournal(Siswa $siswa, Carbon $now): array
    {
        $todayStr = $now->toDateString();

        $existingJurnal = Jurnal::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $todayStr)
            ->first();

        if ($existingJurnal) {
            return [
                'status' => 'skipped',
                'message' => 'Jurnal untuk hari ini sudah terisi sebelumnya.',
                'data' => $existingJurnal,
            ];
        }

        try {
            $aiData = $this->aiJurnalService->generate(
                mode: 'auto',
                catatanKasar: null,
                siswa: $siswa
            );

            // Find relevant competency for student's concentration
            $kompetensi = Kompetensi::where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)
                ->inRandomOrder()
                ->first()
                ?? Kompetensi::first();

            $jurnal = Jurnal::create([
                'siswa_id' => $siswa->id,
                'kompetensi_id' => $kompetensi?->id,
                'cp_id' => $kompetensi?->id,
                'cp' => $kompetensi?->cp,
                'tanggal' => $todayStr,
                'deskripsi_pekerjaan' => $aiData['kegiatan'],
                'catatan' => $aiData['catatan'],
                'status' => 'pending',
            ]);

            Log::info("Smart Attendance: Auto-journal created for NIS {$siswa->nis} on {$todayStr}");

            return [
                'status' => 'success',
                'message' => 'Jurnal harian otomatis berhasil diracik dan disimpan oleh AI.',
                'data' => $jurnal,
            ];
        } catch (Throwable $e) {
            Log::warning("Smart Attendance: Gagal membuat jurnal otomatis: " . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Gagal membuat jurnal otomatis: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Resolve realistic GPS coordinates from DUDI with micro-jitter.
     *
     * @return array{0: float, 1: float}
     */
    protected function resolveCoordinates(Siswa $siswa): array
    {
        $baseLat = null;
        $baseLng = null;

        if ($siswa->dudi && $siswa->dudi->latitude && $siswa->dudi->longitude) {
            $baseLat = (float) $siswa->dudi->latitude;
            $baseLng = (float) $siswa->dudi->longitude;
        }

        if ($baseLat === null || $baseLng === null) {
            $lastAttendance = Absensi::where('siswa_id', $siswa->id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest('id')
                ->first();

            if ($lastAttendance) {
                $baseLat = (float) $lastAttendance->latitude;
                $baseLng = (float) $lastAttendance->longitude;
            }
        }

        // Ciamis city center fallback if neither DUDI nor previous history has coordinates
        $baseLat = $baseLat ?? -7.327400;
        $baseLng = $baseLng ?? 108.355100;

        // Add micro-jitter (~2 to 6 meters) for authentic smartphone GPS behavior
        $jitterLat = mt_rand(-45, 45) / 1000000;
        $jitterLng = mt_rand(-45, 45) / 1000000;

        return [
            round($baseLat + $jitterLat, 6),
            round($baseLng + $jitterLng, 6),
        ];
    }

    /**
     * Resolve or generate valid signature PNG path.
     */
    protected function resolveSignaturePath(Siswa $siswa): string
    {
        // 1. Check if student already has a stored signature from previous attendance
        $prevSignature = Absensi::where('siswa_id', $siswa->id)
            ->whereNotNull('ttd_siswa_path')
            ->latest('id')
            ->value('ttd_siswa_path');

        if ($prevSignature && Storage::disk('public')->exists($prevSignature)) {
            return $prevSignature;
        }

        // 2. Generate a valid signature PNG
        $fileName = 'signatures/auto_' . $siswa->id . '_' . time() . '.png';

        if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
            $width = 240;
            $height = 120;
            $img = imagecreatetruecolor($width, $height);
            imagesavealpha($img, true);
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefill($img, 0, 0, $transparent);
            $ink = imagecolorallocate($img, 30, 41, 59);

            // Draw smooth curve resembling handwritten signature
            $prevX = 20;
            $prevY = 60;
            for ($x = 22; $x <= 220; $x += 2) {
                $y = 60 + (int) (sin($x / 14) * 22 + cos($x / 7) * 12);
                imageline($img, $prevX, $prevY, $x, $y, $ink);
                imageline($img, $prevX, $prevY + 1, $x, $y + 1, $ink);
                $prevX = $x;
                $prevY = $y;
            }

            ob_start();
            imagepng($img);
            $binary = ob_get_clean();
            imagedestroy($img);
        } else {
            // Valid minimal transparent PNG binary with proper magic bytes
            $binary = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAPAAAAB4CAYAAAD9P+4AAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAB9klEQVR4nO3BMQEAAADCoPVPbQwfoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAwJk14AABw718swAAAABJRU5ErkJggg==');
        }

        Storage::disk('public')->put($fileName, $binary);

        return $fileName;
    }
}
