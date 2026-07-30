<?php

namespace App\Imports;

use App\Models\Dudi;
use App\Models\KelasPembimbing;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PembimbingDudi;
use App\Models\PembimbingSekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class ExcelImportService
{
    public function process(string $type, string $filePath, ?int $userId = null): void
    {
        $rows = $this->parseExcel($filePath);
        if (empty($rows)) {
            $this->writeImportResult($type, false, 0, ['File Excel kosong atau tidak valid.'], $filePath);
            return;
        }

        switch ($type) {
            case 'siswa':
                $this->importSiswa($rows, $filePath);
                break;
            case 'dudi':
                $this->importDudi($rows, $filePath);
                break;
            case 'pembimbing_sekolah':
                $this->importPembimbingSekolah($rows, $filePath);
                break;
            case 'pembimbing_dudi':
                $this->importPembimbingDudi($rows, $filePath);
                break;
            case 'kaprog':
                $this->importKaprog($rows, $filePath);
                break;
            default:
                $this->writeImportResult($type, false, 0, ["Tipe impor tidak dikenal: {$type}"], $filePath);
                break;
        }
    }

    public function parseExcel(string $filePath): array
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                return [];
            }

            $headerRow = array_shift($rows);
            $headers = array_map(fn ($h) => strtolower(trim($h ?? '')), $headerRow);

            $data = [];
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn ($val) => !is_null($val) && trim((string) $val) !== ''))) {
                    continue;
                }

                $rowData = [];
                foreach ($headers as $index => $header) {
                    if (empty($header)) {
                        continue;
                    }
                    $rowData[$header] = trim((string) ($row[$index] ?? ''));
                }

                $data[] = $rowData;
            }

            return $data;
        } catch (Throwable $e) {
            return [];
        }
    }

    protected function importSiswa(array $rows, string $filePath): void
    {
        $errors = [];
        $importCount = 0;

        $concentrations = KonsentrasiKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                $rowErrors = [];

                if (empty(array_filter($row))) {
                    continue;
                }

                $validator = Validator::make($row, [
                    'nis' => 'required|numeric',
                    'nama_lengkap' => 'required|string|max:255',
                    'email' => 'nullable|email',
                    'password' => 'required|min:6',
                    'kelas' => 'required|string',
                    'jenis_kelamin' => 'required|in:L,P,l,p',
                    'tahun_ajaran' => 'required|string',
                    'no_hp' => 'nullable',
                    'alamat' => 'nullable',
                    'konsentrasi_keahlian' => 'required',
                ]);

                if ($validator->fails()) {
                    $rowErrors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                }

                if (!empty($row['email']) && User::where('email', $row['email'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar di sistem.";
                }

                if (User::where('username', $row['nis'])->exists() || Siswa::where('nis', $row['nis'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: NIS '{$row['nis']}' sudah terdaftar di sistem.";
                }

                $konName = strtolower(trim($row['konsentrasi_keahlian'] ?? ''));
                if (!isset($concentrations[$konName])) {
                    $rowErrors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$row['konsentrasi_keahlian']}' tidak ditemukan di database.";
                }

                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                $user = User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $row['nis'],
                    'email' => empty($row['email']) ? null : $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'siswa',
                    'is_active' => true,
                ]);

                Siswa::create([
                    'user_id' => $user->id,
                    'konsentrasi_keahlian_id' => $concentrations[$konName],
                    'nis' => $row['nis'],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'kelas' => $row['kelas'],
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin']),
                    'tahun_ajaran' => $row['tahun_ajaran'],
                    'no_hp' => $row['no_hp'] ?: null,
                    'alamat' => $row['alamat'] ?: null,
                    'status_pkl' => 'belum_mulai',
                ]);

                $importCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                $this->writeImportResult($type = 'siswa', false, $importCount, $errors, $filePath);
                return;
            }

            DB::commit();
            $this->writeImportResult('siswa', true, $importCount, [], $filePath);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->writeImportResult('siswa', false, $importCount, ["Terjadi kesalahan sistem saat mengimpor data: {$e->getMessage()}"], $filePath);
        }
    }

    protected function importDudi(array $rows, string $filePath): void
    {
        $errors = [];
        $importCount = 0;

        $concentrations = KonsentrasiKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                $rowErrors = [];

                if (empty(array_filter($row))) {
                    continue;
                }

                $validator = Validator::make($row, [
                    'nama' => 'required|string|max:255',
                    'bidang_usaha' => 'nullable|string',
                    'jenis_industri' => 'nullable|in:pemerintahan,industri,layanan,perdagangan,pendidikan,kesehatan,teknologi,pertanian,lainnya',
                    'alamat' => 'required|string',
                    'latitude' => 'nullable|numeric|between:-90,90',
                    'longitude' => 'nullable|numeric|between:-180,180',
                    'kota' => 'required|string|max:100',
                    'no_telepon' => 'nullable',
                    'email' => 'nullable|email',
                    'nama_pimpinan' => 'nullable|string',
                    'kontak' => 'nullable|string|max:255',
                    'jabatan' => 'nullable|string|max:255',
                    'konsentrasi_keahlian' => 'required',
                ]);

                if ($validator->fails()) {
                    $rowErrors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                }

                if (Dudi::where('nama', $row['nama'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Nama DUDI '{$row['nama']}' sudah terdaftar.";
                }

                $konNames = array_filter(array_map('trim', explode(',', $row['konsentrasi_keahlian'])), fn ($value) => $value !== '');
                $matchingIds = [];
                foreach ($konNames as $name) {
                    $key = strtolower($name);
                    if (!isset($concentrations[$key])) {
                        $rowErrors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$name}' tidak ditemukan di database.";
                    } else {
                        $matchingIds[] = $concentrations[$key];
                    }
                }

                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                $dudi = Dudi::create([
                    'nama' => $row['nama'],
                    'bidang_usaha' => $row['bidang_usaha'] ?: null,
                    'jenis_industri' => $row['jenis_industri'] ?: null,
                    'alamat' => $row['alamat'],
                    'latitude' => $row['latitude'] ?: null,
                    'longitude' => $row['longitude'] ?: null,
                    'kota' => $row['kota'],
                    'no_telepon' => $row['no_telepon'] ?: null,
                    'email' => $row['email'] ?: null,
                    'nama_pimpinan' => $row['nama_pimpinan'] ?: null,
                    'kontak' => $row['kontak'] ?: null,
                    'jabatan' => $row['jabatan'] ?: null,
                    'konsentrasi_keahlian_id' => $matchingIds[0],
                ]);

                $dudi->konsentrasiKeahlians()->sync($matchingIds);
                $importCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                $this->writeImportResult('dudi', false, $importCount, $errors, $filePath);
                return;
            }

            DB::commit();
            $this->writeImportResult('dudi', true, $importCount, [], $filePath);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->writeImportResult('dudi', false, $importCount, ["Terjadi kesalahan sistem saat mengimpor data: {$e->getMessage()}"], $filePath);
        }
    }

    protected function importPembimbingSekolah(array $rows, string $filePath): void
    {
        $errors = [];
        $importCount = 0;

        $concentrations = KonsentrasiKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                $rowErrors = [];

                if (empty(array_filter($row))) {
                    continue;
                }

                $validator = Validator::make($row, [
                    'nip' => 'nullable|numeric',
                    'nama_lengkap' => 'required|string|max:255',
                    'username' => 'required|alpha_dash|max:50',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                    'tipe' => 'required|in:kejuruan,umum,keduanya',
                    'no_hp' => 'nullable',
                    'mapel_cp' => 'nullable',
                    'konsentrasi_keahlian' => 'required',
                    'kelas_diajar' => 'nullable',
                ]);

                if ($validator->fails()) {
                    $rowErrors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                }

                if (User::where('email', $row['email'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar.";
                }

                if (User::where('username', $row['username'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Username '{$row['username']}' sudah terdaftar.";
                }

                if (!empty($row['nip']) && PembimbingSekolah::where('nip', $row['nip'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: NIP '{$row['nip']}' sudah terdaftar.";
                }

                $konName = strtolower(trim($row['konsentrasi_keahlian'] ?? ''));
                if (!isset($concentrations[$konName])) {
                    $rowErrors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$row['konsentrasi_keahlian']}' tidak ditemukan.";
                }

                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                $user = User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'pembimbing_sekolah',
                    'is_active' => true,
                ]);

                $pembimbing = PembimbingSekolah::create([
                    'user_id' => $user->id,
                    'nip' => $row['nip'] ?: null,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'konsentrasi_keahlian_id' => $concentrations[$konName],
                    'tipe' => strtolower($row['tipe']),
                    'no_hp' => $row['no_hp'] ?: null,
                    'mapel_cp' => $row['mapel_cp'] ?: null,
                ]);

                if (!empty($row['kelas_diajar'])) {
                    $classes = array_map('trim', explode(',', $row['kelas_diajar']));
                    foreach ($classes as $cls) {
                        if (empty($cls)) {
                            continue;
                        }
                        KelasPembimbing::create([
                            'pembimbing_sekolah_id' => $pembimbing->id,
                            'kelas' => $cls,
                        ]);
                    }
                }

                $importCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                $this->writeImportResult('pembimbing_sekolah', false, $importCount, $errors, $filePath);
                return;
            }

            DB::commit();
            $this->writeImportResult('pembimbing_sekolah', true, $importCount, [], $filePath);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->writeImportResult('pembimbing_sekolah', false, $importCount, ["Terjadi kesalahan sistem saat mengimpor data: {$e->getMessage()}"], $filePath);
        }
    }

    protected function importPembimbingDudi(array $rows, string $filePath): void
    {
        $errors = [];
        $importCount = 0;

        $dudis = Dudi::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                $rowErrors = [];

                if (empty(array_filter($row))) {
                    continue;
                }

                $validator = Validator::make($row, [
                    'nama_lengkap' => 'required|string|max:255',
                    'username' => 'required|alpha_dash|max:50',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                    'jabatan' => 'nullable|string|max:100',
                    'no_hp' => 'nullable',
                    'nama_perusahaan' => 'required',
                ]);

                if ($validator->fails()) {
                    $rowErrors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                }

                if (User::where('email', $row['email'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar.";
                }

                if (User::where('username', $row['username'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Username '{$row['username']}' sudah terdaftar.";
                }

                $dudiName = strtolower(trim($row['nama_perusahaan'] ?? ''));
                if (!isset($dudis[$dudiName])) {
                    $rowErrors[] = "Baris {$lineNumber}: Perusahaan/DUDI '{$row['nama_perusahaan']}' belum terdaftar di sistem. Daftarkan DUDI terlebih dahulu sebelum mendaftarkan mentor.";
                }

                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                $user = User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'pembimbing_dudi',
                    'is_active' => true,
                ]);

                $pembimbingDudi = PembimbingDudi::create([
                    'user_id' => $user->id,
                    'dudi_id' => $dudis[$dudiName],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'jabatan' => $row['jabatan'] ?: null,
                    'no_hp' => $row['no_hp'] ?: null,
                ]);

                Siswa::where('dudi_id', $dudis[$dudiName])
                    ->whereNull('pembimbing_dudi_id')
                    ->update(['pembimbing_dudi_id' => $pembimbingDudi->id]);

                $importCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                $this->writeImportResult('pembimbing_dudi', false, $importCount, $errors, $filePath);
                return;
            }

            DB::commit();
            $this->writeImportResult('pembimbing_dudi', true, $importCount, [], $filePath);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->writeImportResult('pembimbing_dudi', false, $importCount, ["Terjadi kesalahan sistem saat mengimpor data: {$e->getMessage()}"], $filePath);
        }
    }

    protected function importKaprog(array $rows, string $filePath): void
    {
        $errors = [];
        $importCount = 0;

        $programs = ProgramKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                $rowErrors = [];

                if (empty(array_filter($row))) {
                    continue;
                }

                $validator = Validator::make($row, [
                    'nama_lengkap' => 'required|string|max:255',
                    'username' => 'required|alpha_dash|max:50',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                    'program_keahlian' => 'required',
                ]);

                if ($validator->fails()) {
                    $rowErrors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                }

                if (User::where('email', $row['email'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar.";
                }

                if (User::where('username', $row['username'])->exists()) {
                    $rowErrors[] = "Baris {$lineNumber}: Username '{$row['username']}' sudah terdaftar.";
                }

                $progName = strtolower(trim($row['program_keahlian'] ?? ''));
                if (!isset($programs[$progName])) {
                    $rowErrors[] = "Baris {$lineNumber}: Program keahlian '{$row['program_keahlian']}' tidak ditemukan.";
                }

                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'kaprog',
                    'program_keahlian_id' => $programs[$progName],
                    'is_active' => true,
                ]);

                $importCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                $this->writeImportResult('kaprog', false, $importCount, $errors, $filePath);
                return;
            }

            DB::commit();
            $this->writeImportResult('kaprog', true, $importCount, [], $filePath);
        } catch (Throwable $e) {
            DB::rollBack();
            $this->writeImportResult('kaprog', false, $importCount, ["Terjadi kesalahan sistem saat mengimpor data: {$e->getMessage()}"], $filePath);
        }
    }

    protected function writeImportResult(string $type, bool $success, int $importCount, array $errors, string $filePath): void
    {
        $fileName = sprintf('%s-%s-%s.json', Str::slug($type), now()->format('YmdHis'), Str::random(6));
        $result = [
            'type' => $type,
            'success' => $success,
            'import_count' => $importCount,
            'errors' => $errors,
            'file_path' => $filePath,
            'created_at' => now()->toDateTimeString(),
        ];

        Storage::disk('local')->put("imports/results/{$fileName}", json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
