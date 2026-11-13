<?php

namespace App\Console\Commands;

use App\Models\KonsentrasiKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportSiswaCommand extends Command
{
    protected $signature = 'pkl:import-siswa 
                            {file? : Path file CSV atau Excel}
                            {--include-dkv : Ikut sertakan siswa DKV}
                            {--tahun-ajaran=2025/2026 : Tahun ajaran siswa}';

    protected $description = 'Impor data siswa SMKN 1 Ciamis dengan pemetaan jurusan otomatis dan verifikasi NIS';

    public function handle()
    {
        $filePath = $this->argument('file') ?: storage_path('app/data_siswa_smkn1ciamis.csv');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan di: {$filePath}");
            return 1;
        }

        $includeDkv = $this->option('include-dkv');
        $tahunAjaran = $this->option('tahun-ajaran');

        $this->info("Membaca file data siswa: {$filePath}");
        $content = file_get_contents($filePath);
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $delimiter = str_contains($line, ';') ? ';' : ',';
            $rows[] = str_getcsv($line, $delimiter);
        }

        if (empty($rows)) {
            $this->error("File kosong.");
            return 1;
        }

        // Header check
        $header = array_shift($rows);

        // Fetch concentrations
        $concentrations = [];
        foreach (KonsentrasiKeahlian::all() as $kon) {
            $concentrations[strtolower(trim($kon->nama))] = $kon->id;
            $concentrations[strtolower(trim($kon->kode))] = $kon->id;
        }

        $createdCount = 0;
        $updatedCount = 0;
        $correctedNisCount = 0;
        $skippedCount = 0;
        $correctedNisList = [];
        $processedNis = [];

        $this->output->progressStart(count($rows));

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $this->output->progressAdvance();

                if (count($row) < 3) continue;

                $nama = trim($row[1] ?? '');
                $nis = trim($row[2] ?? '');
                $kelas = trim($row[3] ?? '');

                // Skip header or summary
                if (strtolower($nama) === 'nama lengkap' || strtolower($nis) === 'nis' || strtolower($row[0] ?? '') === 'no') {
                    continue;
                }
                if (str_contains(strtoupper($nama), 'KESELURUHAN') || str_contains(strtoupper($nama), 'TOTAL')) {
                    continue;
                }
                if (!is_numeric($nis) || strlen($nis) < 4) {
                    continue;
                }

                // Prevent duplicates within file
                if (isset($processedNis[$nis])) {
                    continue;
                }
                $processedNis[$nis] = true;

                // Detect concentration ID
                $konId = $this->detectKonsentrasi($kelas, $concentrations);

                // DKV Filter: if not include-dkv, skip DKV (ID 7)
                if ($konId === 7 && !$includeDkv) {
                    $skippedCount++;
                    continue;
                }

                // Check existing student in DB
                $existingByNis = Siswa::where('nis', $nis)->first();

                $existingByName = null;
                if (!$existingByNis && !empty($nama)) {
                    $existingByName = Siswa::whereRaw('LOWER(nama_lengkap) = ?', [strtolower($nama)])
                        ->where('konsentrasi_keahlian_id', $konId)
                        ->first();
                }

                if ($existingByName) {
                    // Correct NIS typo
                    $oldNis = $existingByName->nis;
                    $existingByName->update([
                        'nis' => $nis,
                        'kelas' => $kelas ?: $existingByName->kelas,
                        'konsentrasi_keahlian_id' => $konId,
                        'tahun_ajaran' => $tahunAjaran,
                    ]);
                    if ($existingByName->user) {
                        $existingByName->user->update([
                            'username' => $nis,
                            'name' => $nama,
                        ]);
                    }
                    $correctedNisList[] = "Siswa '{$nama}': NIS di sistem dikoreksi dari {$oldNis} menjadi {$nis}";
                    $correctedNisCount++;
                } elseif ($existingByNis) {
                    // Update details
                    $existingByNis->update([
                        'nama_lengkap' => $nama,
                        'kelas' => $kelas ?: $existingByNis->kelas,
                        'konsentrasi_keahlian_id' => $konId,
                        'tahun_ajaran' => $tahunAjaran,
                    ]);
                    if ($existingByNis->user) {
                        $existingByNis->user->update([
                            'name' => $nama,
                            'username' => $nis,
                        ]);
                    }
                    $updatedCount++;
                } else {
                    // Create new
                    $user = User::where('username', $nis)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $nama,
                            'username' => $nis,
                            'email' => null,
                            'password' => Hash::make($nis),
                            'role' => 'siswa',
                            'is_active' => true,
                            'force_password_change' => false,
                        ]);
                    }

                    Siswa::create([
                        'user_id' => $user->id,
                        'konsentrasi_keahlian_id' => $konId,
                        'nis' => $nis,
                        'nama_lengkap' => $nama,
                        'kelas' => $kelas,
                        'jenis_kelamin' => 'L',
                        'tahun_ajaran' => $tahunAjaran,
                        'status_pkl' => 'belum_mulai',
                    ]);

                    $createdCount++;
                }
            }

            DB::commit();
            $this->output->progressFinish();

            $this->info("\n=== HASIL IMPOR SISWA ===");
            $this->line("Siswa baru ditambahkan: {$createdCount}");
            $this->line("Siswa diperbarui: {$updatedCount}");
            $this->line("NIS dikoreksi: {$correctedNisCount}");
            $this->line("Siswa dilewati (DKV): {$skippedCount}");
            $this->line("Total siswa di database sekarang: " . Siswa::count());

            if (!empty($correctedNisList)) {
                $this->warn("\nRincian Koreksi NIS:");
                foreach ($correctedNisList as $item) {
                    $this->line(" - {$item}");
                }
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nTerjadi error saat impor: " . $e->getMessage());
            return 1;
        }
    }

    protected function detectKonsentrasi($kelas, $concentrations)
    {
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $kelas));

        if (str_contains($clean, 'PPLG') || str_contains($clean, 'RPL') || str_contains($clean, 'GIM')) {
            return $concentrations['pplg'] ?? 8;
        }
        if (str_contains($clean, 'DKV') || str_contains($clean, 'MM') || str_contains($clean, 'MULTIMEDIA')) {
            return $concentrations['dkv'] ?? 7;
        }
        if (str_contains($clean, 'PBS') || str_contains($clean, 'SYARIAH')) {
            return $concentrations['pbs'] ?? 2;
        }
        if (str_contains($clean, 'AK') || str_contains($clean, 'AKL') || str_contains($clean, 'AKUNTANSI')) {
            return $concentrations['akl'] ?? $concentrations['akuntansi'] ?? 1;
        }
        if (str_contains($clean, 'MP') || str_contains($clean, 'MPLB') || str_contains($clean, 'OTKP') || str_contains($clean, 'PERKANTORAN')) {
            return $concentrations['mplb'] ?? $concentrations['manajemen perkantoran'] ?? 3;
        }
        if (str_contains($clean, 'PBD') || str_contains($clean, 'BD') || str_contains($clean, 'PDG') || str_contains($clean, 'DIGITAL')) {
            return $concentrations['pbd'] ?? $concentrations['bisnis digital'] ?? 5;
        }
        if (str_contains($clean, 'PBR') || str_contains($clean, 'RITEL') || str_contains($clean, 'PM') || str_contains($clean, 'BSR')) {
            return $concentrations['pbr'] ?? $concentrations['bisnis ritel'] ?? 4;
        }
        if (str_contains($clean, 'HTL') || str_contains($clean, 'HOTEL') || str_contains($clean, 'AP') || str_contains($clean, 'PERHOTELAN')) {
            return $concentrations['htl'] ?? $concentrations['perhotelan'] ?? 6;
        }
        if (str_contains($clean, 'KLN') || str_contains($clean, 'KULINER') || str_contains($clean, 'TBG') || str_contains($clean, 'BOGA')) {
            return $concentrations['kln'] ?? $concentrations['kuliner'] ?? 9;
        }

        return 1;
    }
}
