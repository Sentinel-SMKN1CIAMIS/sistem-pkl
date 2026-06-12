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
            $headers = ['nama_lengkap', 'username', 'email', 'password', 'konsentrasi_keahlian'];
            $exampleRow = ['Drs. Ahmad Yusuf, M.T.', 'ahmadyusuf', 'ahmad@example.com', 'kaprog123', 'Rekayasa Perangkat Lunak'];
            $filename = 'template_kaprog.xlsx';
        } else {
            abort(404);
        }
        
        // Populate header
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }
        
        // Populate example row
        foreach ($exampleRow as $colIndex => $value) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            if (is_numeric($value) && strlen($value) > 8) {
                $sheet->setCellValueExplicit($colLetter . '2', $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue($colLetter . '2', $value);
            }
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
     * Parse Excel (.xlsx, .xls) files natively.
     */
    private function parseExcel($file)
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            if (empty($rows)) return [];
            
            // First row contains the headers
            $headerRow = array_shift($rows);
            $headers = array_map(fn($h) => strtolower(trim($h ?? '')), $headerRow);
            
            $data = [];
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim($val) !== ''))) {
                    continue;
                }
                
                $rowData = [];
                foreach ($headers as $index => $header) {
                    if (empty($header)) continue;
                    $rowData[$header] = trim($row[$index] ?? '');
                }
                $data[] = $rowData;
            }
            
            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Import Siswa (Student) Data.
     */
    public function importSiswa(Request $request)
    {
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
                $lineNumber = $index + 2; // Accounting for header line (1-indexed)

                // Skip completely empty rows
                if (empty(array_filter($row))) continue;

                // Validate Row Data
                $validator = Validator::make($row, [
                    'nis' => 'required|numeric',
                    'nama_lengkap' => 'required|string|max:255',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                    'kelas' => 'required|string',
                    'jenis_kelamin' => 'required|in:L,P,l,p',
                    'tahun_ajaran' => 'required|string',
                    'no_hp' => 'nullable',
                    'alamat' => 'nullable',
                    'konsentrasi_keahlian' => 'required',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris {$lineNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Check duplicates in database
                if (User::where('email', $row['email'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: Email '{$row['email']}' sudah terdaftar di sistem.";
                    continue;
                }
                if (User::where('username', $row['nis'])->exists() || Siswa::where('nis', $row['nis'])->exists()) {
                    $errors[] = "Baris {$lineNumber}: NIS '{$row['nis']}' sudah terdaftar di sistem.";
                    continue;
                }

                // Match Concentration Keahlian
                $konName = strtolower(trim($row['konsentrasi_keahlian']));
                if (!isset($concentrations[$konName])) {
                    $errors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$row['konsentrasi_keahlian']}' tidak ditemukan di database.";
                    continue;
                }

                // If no errors so far, proceed to create
                if (empty($errors)) {
                    // Create User Account
                    $user = User::create([
                        'name' => $row['nama_lengkap'],
                        'username' => $row['nis'],
                        'email' => $row['email'],
                        'password' => Hash::make($row['password']),
                        'role' => 'siswa',
                        'is_active' => true,
                    ]);

                    // Create Siswa Profile
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
            }

            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('import_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimpor {$importCount} data siswa.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Import DUDI (Industri/Company) Data.
     */
    public function importDudi(Request $request)
    {
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
                    'tipe' => 'required|in:kejuruan,umum',
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
                    'jabatan' => 'required|string|max:100',
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

                    PembimbingDudi::create([
                        'user_id' => $user->id,
                        'dudi_id' => $dudis[$dudiName],
                        'nama_lengkap' => $row['nama_lengkap'],
                        'jabatan' => $row['jabatan'],
                        'no_hp' => $row['no_hp'] ?: null,
                    ]);

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
                    'nama_lengkap' => 'required|string|max:255',
                    'username' => 'required|alpha_dash|max:50',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                    'konsentrasi_keahlian' => 'required',
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

                $konName = strtolower(trim($row['konsentrasi_keahlian']));
                if (!isset($concentrations[$konName])) {
                    $errors[] = "Baris {$lineNumber}: Konsentrasi keahlian '{$row['konsentrasi_keahlian']}' tidak ditemukan.";
                    continue;
                }

                if (empty($errors)) {
                    User::create([
                        'name' => $row['nama_lengkap'],
                        'username' => $row['username'],
                        'email' => $row['email'],
                        'password' => Hash::make($row['password']),
                        'role' => 'kaprog',
                        'konsentrasi_keahlian_id' => $concentrations[$konName],
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

