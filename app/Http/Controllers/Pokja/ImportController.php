<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use App\Models\KelasPembimbing;
use App\Models\KonsentrasiKeahlian;

class ImportController extends Controller
{
    /**
     * Download Excel Template with professional styling (auto-fit columns, bold headers).
     */
    public function downloadTemplate($type)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = [];
        $exampleRow = [];
        $filename = '';
        
        if ($type === 'siswa') {
            $headers = ['nis', 'nama_lengkap', 'email', 'password', 'kelas', 'jenis_kelamin', 'tahun_ajaran', 'no_hp', 'alamat', 'konsentrasi_keahlian'];
            $exampleRow = ['12345678', 'Ahmad Fauzi', 'ahmad@example.com', 'password123', 'XII RPL 1', 'L', '2025/2026', '081234567890', 'Jl. Merdeka No. 10 Ciamis', 'Rekayasa Perangkat Lunak'];
            $filename = 'template_siswa.xlsx';
        } elseif ($type === 'dudi') {
            $headers = ['nama', 'bidang_usaha', 'jenis_industri', 'alamat', 'latitude', 'longitude', 'kota', 'no_telepon', 'email', 'nama_pimpinan', 'kontak', 'jabatan', 'konsentrasi_keahlian'];
            $exampleRow = ['PT Solusi Digital', 'Teknologi Informasi', 'teknologi', 'Jl. Asia Afrika No. 45', '-6.914744', '107.609810', 'Bandung', '022123456', 'contact@solusidigital.com', 'Budi Santoso', 'Ahmad Wijaya', 'HRD Manager', 'Rekayasa Perangkat Lunak, Teknik Komputer Jaringan'];
            $filename = 'template_dudi.xlsx';
        } elseif ($type === 'pembimbing_sekolah') {
            $headers = ['nip', 'nama_lengkap', 'username', 'email', 'password', 'tipe', 'no_hp', 'mapel_cp', 'konsentrasi_keahlian', 'kelas_diajar'];
            $exampleRow = ['198501012010011002', 'Drs. H. Hendra Wijaya', 'hendrawijaya', 'hendra@example.com', 'pembimbing123', 'kejuruan', '081398765432', 'Pemrograman Web & Mobile', 'Rekayasa Perangkat Lunak', 'XII RPL 1, XII RPL 2'];
            $filename = 'template_pembimbing_sekolah.xlsx';
        } elseif ($type === 'pembimbing_dudi') {
            $headers = ['nama_lengkap', 'username', 'email', 'password', 'jabatan', 'no_hp', 'nama_perusahaan'];
            $exampleRow = ['Eko Prasetyo', 'ekoprasetyo', 'eko@example.com', 'mentor123', 'Senior Developer', '085712345678', 'PT Solusi Digital'];
            $filename = 'template_pembimbing_dudi.xlsx';
        } elseif ($type === 'kaprog') {
            $headers = ['nama_lengkap', 'username', 'email', 'password', 'program_keahlian'];
            $exampleRow = ['Drs. Ahmad Yusuf, M.T.', 'ahmadyusuf', 'ahmad@example.com', 'kaprog123', 'Pengembangan Perangkat Lunak dan Gim'];
            $filename = 'template_kaprog.xlsx';
        } else {
            abort(404);
        }
        
        // Populate header
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }
        
        // Populate example row and formulas
        if ($type === 'pembimbing_dudi') {
            // Row 2 is the example row, but uses formulas for username, email, password
            $sheet->setCellValue('A2', 'Eko Prasetyo');
            $sheet->setCellValue('B2', '=IF(A2="","",LOWER(SUBSTITUTE(A2," ","")))');
            $sheet->setCellValue('C2', '=IF(B2="","",B2&"@dudi.pkl.id")');
            $sheet->setCellValue('D2', '=IF(A2="","","pembimbing123")');
            $sheet->setCellValue('E2', 'Senior Developer');
            $sheet->setCellValueExplicit('F2', '085712345678', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('G2', 'PT Solusi Digital');

            // Populate formulas for rows 3 to 200
            for ($row = 3; $row <= 200; $row++) {
                $sheet->setCellValue('B' . $row, '=IF(A' . $row . '="","",LOWER(SUBSTITUTE(A' . $row . '," ","")))');
                $sheet->setCellValue('C' . $row, '=IF(B' . $row . '="","",B' . $row . '&"@dudi.pkl.id")');
                $sheet->setCellValue('D' . $row, '=IF(A' . $row . '="","","pembimbing123")');
            }

            // Set column F (no_hp) format to Text to preserve leading zeros
            $sheet->getStyle('F2:F200')->getNumberFormat()->setFormatCode('@');
        } else {
            // Populate example row for other types
            foreach ($exampleRow as $colIndex => $value) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                if (is_numeric($value) && strlen($value) > 8) {
                    $sheet->setCellValueExplicit($colLetter . '2', $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter . '2', $value);
                }
            }
        }

        // Apply text formatting for phone numbers in other templates to preserve leading zeros
        if ($type === 'siswa') {
            $sheet->getStyle('H2:H200')->getNumberFormat()->setFormatCode('@');
        } elseif ($type === 'dudi') {
            $sheet->getStyle('H2:H200')->getNumberFormat()->setFormatCode('@');
        } elseif ($type === 'pembimbing_sekolah') {
            $sheet->getStyle('G2:G200')->getNumberFormat()->setFormatCode('@');
        }
        
        // Styling headers
        $totalCols = count($headers);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
        
        $headerRange = 'A1:' . $lastColLetter . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Dark Blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Styling example row (italic slate-500)
        $exampleRange = 'A2:' . $lastColLetter . '2';
        $sheet->getStyle($exampleRange)->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '64748B'],
            ],
        ]);
        
        // Auto size columns
        for ($col = 1; $col <= $totalCols; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        
        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('2')->setRowHeight(20);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Parse Excel (.xlsx, .xls) and CSV (.csv, .txt) files flexibly.
     */
    private function parseExcel($file)
    {
        try {
            $ext = strtolower($file->getClientOriginalExtension());
            $rows = [];

            if (in_array($ext, ['csv', 'txt'])) {
                $content = file_get_contents($file->getRealPath());
                $lines = preg_split('/\r\n|\r|\n/', trim($content));
                foreach ($lines as $line) {
                    if (trim($line) === '') continue;
                    $delimiter = str_contains($line, ';') ? ';' : ',';
                    $rows[] = str_getcsv($line, $delimiter);
                }
            } else {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            }

            if (empty($rows)) return [];

            // Find header row (first non-empty row)
            $headerRow = null;
            while (!empty($rows)) {
                $candidate = array_shift($rows);
                if (!empty(array_filter($candidate, fn($v) => !is_null($v) && trim((string) $v) !== ''))) {
                    $headerRow = $candidate;
                    break;
                }
            }

            if (!$headerRow) return [];

            // Normalize header names
            $headers = [];
            foreach ($headerRow as $idx => $h) {
                $clean = strtolower(trim((string) $h));
                $clean = str_replace([' ', '-', '.'], '_', $clean);
                if (in_array($clean, ['nama', 'nama_siswa', 'student_name'])) $clean = 'nama_lengkap';
                if (in_array($clean, ['nisn', 'nomor_induk', 'username'])) $clean = 'nis';
                if (in_array($clean, ['rombel', 'class'])) $clean = 'kelas';
                if (in_array($clean, ['jk', 'gender', 'sex', 'l/p'])) $clean = 'jenis_kelamin';
                if (in_array($clean, ['jurusan', 'kompetensi_keahlian', 'keahlian'])) $clean = 'konsentrasi_keahlian';
                if (in_array($clean, ['hp', 'telp', 'telepon', 'whatsapp', 'wa', 'no_telepon'])) $clean = 'no_hp';
                $headers[$idx] = $clean;
            }

            $data = [];
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string) $val) !== ''))) {
                    continue;
                }

                $rowData = [];
                foreach ($headers as $index => $header) {
                    if (empty($header)) continue;
                    $rowData[$header] = trim((string) ($row[$index] ?? ''));
                }
                $data[] = $rowData;
            }

            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import parse error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Intelligently detect concentration from class name or text.
     */
    protected function detectKonsentrasiFromKelas($kelas, $concentrations)
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

        return null;
    }

    /**
     * Import Siswa (Student) Data with flexible columns, smart deduplication, NIS verification, and Jurusan filtering.
     */
    public function importSiswa(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        $rows = $this->parseExcel($request->file('file'));
        if (empty($rows)) {
            return back()->with('error', 'File Excel atau CSV kosong atau tidak valid.');
        }

        $selectedJurusan = $request->input('selected_jurusan');
        if (!is_array($selectedJurusan) || empty($selectedJurusan)) {
            // Default: all concentrations EXCEPT DKV (ID: 7) as requested
            $selectedJurusan = KonsentrasiKeahlian::where('kode', '!=', 'DKV')->pluck('id')->toArray();
        } else {
            $selectedJurusan = array_map('intval', $selectedJurusan);
        }

        $defaultTahunAjaran = $request->input('tahun_ajaran') ?: '2025/2026';
        $syncMode = $request->boolean('sync_mode', true);

        // Fetch Concentrations mapping for fast lookup
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

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                // Skip empty row
                if (empty(array_filter($row))) continue;

                $nama = trim($row['nama_lengkap'] ?? '');
                $nis = trim($row['nis'] ?? '');
                $kelas = trim($row['kelas'] ?? '');

                // Skip repeated headers or summary lines
                if (strtolower($nama) === 'nama lengkap' || strtolower($nis) === 'nis' || strtolower($nama) === 'no') {
                    continue;
                }
                if (str_contains(strtoupper($nama), 'KESELURUHAN') || str_contains(strtoupper($nama), 'TOTAL') || str_contains(strtoupper($row['no'] ?? ''), 'TOTAL')) {
                    continue;
                }

                // Check NIS validity (numeric and >= 4 characters)
                if (!is_numeric($nis) || strlen($nis) < 4) {
                    continue;
                }

                // Skip duplicate NIS within the same file upload
                if (isset($processedNis[$nis])) {
                    continue;
                }
                $processedNis[$nis] = true;

                // Determine Concentration ID
                $konId = null;
                if (!empty($row['konsentrasi_keahlian'])) {
                    $searchKon = strtolower(trim($row['konsentrasi_keahlian']));
                    $konId = $concentrations[$searchKon] ?? null;
                }
                if (!$konId && !empty($kelas)) {
                    $konId = $this->detectKonsentrasiFromKelas($kelas, $concentrations);
                }

                if (!$konId) {
                    $konId = 1;
                }

                // Check Jurusan Filter: If this concentration is NOT selected, skip!
                if (!in_array($konId, $selectedJurusan)) {
                    $skippedCount++;
                    continue;
                }

                $email = !empty($row['email']) ? trim($row['email']) : null;
                $password = !empty($row['password']) ? trim($row['password']) : $nis;
                $jk = !empty($row['jenis_kelamin']) ? strtoupper(substr(trim($row['jenis_kelamin']), 0, 1)) : 'L';
                if (!in_array($jk, ['L', 'P'])) $jk = 'L';
                $tahunAjaran = !empty($row['tahun_ajaran']) ? trim($row['tahun_ajaran']) : $defaultTahunAjaran;
                $noHp = !empty($row['no_hp']) ? trim($row['no_hp']) : null;
                $alamat = !empty($row['alamat']) ? trim($row['alamat']) : null;

                // Match existing student
                $existingByNis = Siswa::where('nis', $nis)->first();

                // If not found by NIS, check by exact name (case-insensitive) to verify if NIS was mistyped in system
                $existingByName = null;
                if (!$existingByNis && !empty($nama)) {
                    $existingByName = Siswa::whereRaw('LOWER(nama_lengkap) = ?', [strtolower($nama)])
                        ->where('konsentrasi_keahlian_id', $konId)
                        ->first();
                }

                if ($existingByName && $syncMode) {
                    // DISCREPANCY FOUND & CORRECTED: Student exists in system with different/typo NIS!
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
                } elseif ($existingByNis && $syncMode) {
                    // Student already exists by NIS: update details to match official file
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
                } elseif (!$existingByNis && !$existingByName) {
                    // New Student: Create User and Siswa
                    if ($email && User::where('email', $email)->exists()) {
                        $email = null;
                    }

                    $user = User::where('username', $nis)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $nama,
                            'username' => $nis,
                            'email' => $email,
                            'password' => Hash::make($password),
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
                        'jenis_kelamin' => $jk,
                        'tahun_ajaran' => $tahunAjaran,
                        'no_hp' => $noHp,
                        'alamat' => $alamat,
                        'status_pkl' => 'belum_mulai',
                    ]);

                    $createdCount++;
                }
            }

            DB::commit();

            $msg = "Impor data siswa selesai: {$createdCount} siswa baru ditambahkan, {$updatedCount} siswa diperbarui";
            if ($correctedNisCount > 0) {
                $msg .= ", {$correctedNisCount} data NIS siswa dikoreksi";
            }
            if ($skippedCount > 0) {
                $msg .= ", {$skippedCount} siswa dilewati (karena jurusan tidak dipilih/DKV)";
            }
            $msg .= ".";

            $redirect = back()->with('success', $msg);
            if (!empty($correctedNisList)) {
                $redirect->with('nis_corrections', $correctedNisList);
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengimpor data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Import DUDI (Industri/Company) Data.
     */
    public function importDudi(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $rows = $this->parseExcel($request->file('file'));
        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        $errors = [];
        $importCount = 0;

        // Fetch Concentrations mapping for fast lookup
        $concentrations = KonsentrasiKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;

                if (empty(array_filter($row))) continue;

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
                    $errors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                if (Dudi::where('nama', $row['nama'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Nama DUDI '{$row['nama']}' sudah terdaftar.";
                    continue;
                }

                // Match concentrations (comma-separated support)
                $konNames = array_map('trim', explode(',', $row['konsentrasi_keahlian']));
                $matchingIds = [];
                
                foreach ($konNames as $name) {
                    $key = strtolower($name);
                    if (!isset($concentrations[$key])) {
                        $errors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$name}' tidak ditemukan di database.";
                    } else {
                        $matchingIds[] = $concentrations[$key];
                    }
                }

                if (empty($errors)) {
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
                        'konsentrasi_keahlian_id' => $matchingIds[0], // primary relation
                    ]);

                    // Sync pivot table
                    $dudi->konsentrasiKeahlians()->sync($matchingIds);
                    $importCount++;
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('import_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimpor {$importCount} data DUDI.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Import Pembimbing Sekolah Data.
     */
    public function importPembimbingSekolah(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $rows = $this->parseExcel($request->file('file'));
        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        $errors = [];
        $importCount = 0;

        $concentrations = KonsentrasiKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;

                if (empty(array_filter($row))) continue;

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
                    $errors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                if (User::where('email', $row['email'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar.";
                    continue;
                }
                if (User::where('username', $row['username'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Username '{$row['username']}' sudah terdaftar.";
                    continue;
                }
                if (!empty($row['nip']) && PembimbingSekolah::where('nip', $row['nip'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: NIP '{$row['nip']}' sudah terdaftar.";
                    continue;
                }

                $konName = strtolower(trim($row['konsentrasi_keahlian']));
                if (!isset($concentrations[$konName])) {
                    $errors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$row['konsentrasi_keahlian']}' tidak ditemukan.";
                    continue;
                }

                if (empty($errors)) {
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

                    // Assign classes
                    if (!empty($row['kelas_diajar'])) {
                        $classes = array_map('trim', explode(',', $row['kelas_diajar']));
                        foreach ($classes as $cls) {
                            if (empty($cls)) continue;
                            KelasPembimbing::create([
                                'pembimbing_sekolah_id' => $pembimbing->id,
                                'kelas' => $cls
                            ]);
                        }
                    }

                    $importCount++;
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('import_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimpor {$importCount} data pembimbing sekolah.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Import Pembimbing DUDI (Mentor Industri) Data.
     */
    public function importPembimbingDudi(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $rows = $this->parseExcel($request->file('file'));
        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        $errors = [];
        $importCount = 0;

        // Fetch DUDIs for mapping
        $dudis = Dudi::pluck('id', 'nama')
            ->mapWithKeys(fn($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;

                if (empty(array_filter($row))) continue;

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
                    $errors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                if (User::where('email', $row['email'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar.";
                    continue;
                }
                if (User::where('username', $row['username'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Username '{$row['username']}' sudah terdaftar.";
                    continue;
                }

                $dudiName = strtolower(trim($row['nama_perusahaan']));
                if (!isset($dudis[$dudiName])) {
                    $errors[] = "Baris {$lineNumber}: Perusahaan/DUDI '{$row['nama_perusahaan']}' belum terdaftar di sistem. Daftarkan DUDI terlebih dahulu sebelum mendaftarkan mentor.";
                    continue;
                }

                if (empty($errors)) {
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
                        'jabatan' => $row['jabatan'],
                        'no_hp' => $row['no_hp'] ?: null,
                    ]);

                    Siswa::where('dudi_id', $dudis[$dudiName])
                        ->whereNull('pembimbing_dudi_id')
                        ->update(['pembimbing_dudi_id' => $pembimbingDudi->id]);

                    $importCount++;
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('import_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimpor {$importCount} data pembimbing DUDI.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Import Kaprog (Head of Program) Data.
     */
    public function importKaprog(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $rows = $this->parseExcel($request->file('file'));
        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        $errors = [];
        $importCount = 0;

        $programs = \App\Models\ProgramKeahlian::pluck('id', 'nama')
            ->mapWithKeys(fn($id, $nama) => [strtolower(trim($nama)) => $id]);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;

                if (empty(array_filter($row))) continue;

                $validator = Validator::make($row, [
                    'nama_lengkap' => 'required|string|max:255',
                    'username' => 'required|alpha_dash|max:50',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                    'program_keahlian' => 'required',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                if (User::where('email', $row['email'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar.";
                    continue;
                }
                if (User::where('username', $row['username'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Username '{$row['username']}' sudah terdaftar.";
                    continue;
                }

                $progName = strtolower(trim($row['program_keahlian']));
                if (!isset($programs[$progName])) {
                    $errors[] = "Baris {$lineNumber}: Program keahlian '{$row['program_keahlian']}' tidak ditemukan.";
                    continue;
                }

                if (empty($errors)) {
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
            }

            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('import_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimpor {$importCount} data Kaprog.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Show premium interactive Import Guide with database values list.
     */
    public function showPanduan()
    {
        $jurusans = \App\Models\KonsentrasiKeahlian::orderBy('nama')->pluck('nama');
        $dudis = \App\Models\Dudi::orderBy('nama')->pluck('nama');

        return view('pokja.import.panduan', compact('jurusans', 'dudis'));
    }
}

