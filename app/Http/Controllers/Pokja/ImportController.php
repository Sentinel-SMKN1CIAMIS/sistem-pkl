<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ImportExcelJob;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use App\Models\KelasPembimbing;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Support\Facades\Storage;

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

        $filePath = $request->file('file')->store('imports/excel', 'local');
        ImportExcelJob::dispatch('siswa', $filePath, optional(auth()->user())->id);

        return back()->with('success', 'Proses impor siswa telah dijadwalkan. Silakan jalankan worker queue untuk memproses file.');
    }

    /**
     * Import DUDI (Industri/Company) Data.
     */
    public function importDudi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $filePath = $request->file('file')->store('imports/excel', 'local');
        ImportExcelJob::dispatch('dudi', $filePath, optional(auth()->user())->id);

        return back()->with('success', 'Proses impor DUDI telah dijadwalkan. Silakan jalankan worker queue untuk memproses file.');
    }

    /**
     * Import Pembimbing Sekolah Data.
     */
    public function importPembimbingSekolah(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $filePath = $request->file('file')->store('imports/excel', 'local');
        ImportExcelJob::dispatch('pembimbing_sekolah', $filePath, optional(auth()->user())->id);

        return back()->with('success', 'Proses impor pembimbing sekolah telah dijadwalkan. Silakan jalankan worker queue untuk memproses file.');
    }

    /**
     * Import Pembimbing DUDI (Mentor Industri) Data.
     */
    public function importPembimbingDudi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $filePath = $request->file('file')->store('imports/excel', 'local');
        ImportExcelJob::dispatch('pembimbing_dudi', $filePath, optional(auth()->user())->id);

        return back()->with('success', 'Proses impor pembimbing DUDI telah dijadwalkan. Silakan jalankan worker queue untuk memproses file.');
    }

    /**
     * Import Kaprog (Head of Program) Data.
     */
    public function importKaprog(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $filePath = $request->file('file')->store('imports/excel', 'local');
        ImportExcelJob::dispatch('kaprog', $filePath, optional(auth()->user())->id);

        return back()->with('success', 'Proses impor Kaprog telah dijadwalkan. Silakan jalankan worker queue untuk memproses file.');
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

