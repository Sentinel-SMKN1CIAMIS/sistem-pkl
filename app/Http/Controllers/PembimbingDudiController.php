<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PembimbingDudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\PembimbingDudi::with(['user', 'dudi']);
        if (auth()->user()->konsentrasi_keahlian_id) {
            $userKonId = auth()->user()->konsentrasi_keahlian_id;
            $query->whereHas('dudi', function($q) use ($userKonId) {
                $q->where('konsentrasi_keahlian_id', $userKonId)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                      $sub->where('konsentrasi_keahlians.id', $userKonId);
                  });
            });
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $query->whereHas('dudi', function($q) use ($konsentrasiIds) {
                $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                      $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                  });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('dudi', function($d) use ($search) {
                      $d->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['nama_lengkap', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->latest();
        }

        $mentors = $query->paginate(15)->withQueryString();
        return view('pokja.pembimbing-dudi.index', compact('mentors'));
    }

    public function create()
    {
        $dudis = \App\Models\Dudi::all();
        $manualMentors = \App\Models\Siswa::whereNotNull('pembimbing_dudi_nama')
            ->whereNull('pembimbing_dudi_id')
            ->with('dudi')
            ->get();
        return view('pokja.pembimbing-dudi.create', compact('dudis', 'manualMentors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'dudi_id' => 'required|exists:dudis,id',
            'no_hp' => 'nullable|string',
            'siswa_id' => 'nullable|exists:siswas,id',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'pembimbing_dudi',
        ]);

        $pembimbingDudi = \App\Models\PembimbingDudi::create(array_merge($request->all(), ['user_id' => $user->id]));

        if ($request->filled('siswa_id')) {
            $siswa = \App\Models\Siswa::find($request->siswa_id);
            if ($siswa) {
                $siswa->update([
                    'pembimbing_dudi_id' => $pembimbingDudi->id
                ]);
            }
        }

        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Pembimbing DUDI berhasil ditambahkan.');
    }

    public function edit(\App\Models\PembimbingDudi $pembimbing_dudi)
    {
        $dudis = \App\Models\Dudi::all();
        return view('pokja.pembimbing-dudi.edit', compact('pembimbing_dudi', 'dudis'));
    }

    public function update(Request $request, \App\Models\PembimbingDudi $pembimbing_dudi)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'dudi_id' => 'required|exists:dudis,id',
            'no_hp' => 'nullable|string',
        ]);

        $pembimbing_dudi->update($request->all());
        $pembimbing_dudi->user->update(['name' => $request->nama_lengkap]);

        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Data pembimbing DUDI berhasil diperbarui.');
    }

    public function destroy(\App\Models\PembimbingDudi $pembimbing_dudi)
    {
        $user = $pembimbing_dudi->user;
        $pembimbing_dudi->delete();
        if ($user) {
            $user->delete();
        }
        return redirect()->route('pokja.pembimbing_dudi.index')
            ->with('success', 'Data pembimbing DUDI berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $query = \App\Models\PembimbingDudi::with(['user', 'dudi']);

        if (auth()->user()->konsentrasi_keahlian_id) {
            $userKonId = auth()->user()->konsentrasi_keahlian_id;
            $query->whereHas('dudi', function($q) use ($userKonId) {
                $q->where('konsentrasi_keahlian_id', $userKonId)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                      $sub->where('konsentrasi_keahlians.id', $userKonId);
                  });
            });
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $query->whereHas('dudi', function($q) use ($konsentrasiIds) {
                $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                      $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                  });
            });
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhereHas('dudi', function($d) use ($search) {
                          $d->where('nama', 'like', "%{$search}%");
                      });
                });
            }
        }

        $mentors = $query->orderBy('nama_lengkap')->get();

        $kopKeys = [
            'report_kop_baris_1' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT',
            'report_kop_baris_2' => 'DINAS PENDIDIKAN',
            'report_kop_baris_3' => 'CABANG DINAS PENDIDIKAN WILAYAH XIII',
            'report_kop_baris_4' => 'SMK NEGERI 1 CIAMIS',
            'report_kop_baris_5' => 'Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204',
            'report_kop_baris_6' => 'Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net',
            'report_kop_baris_7' => 'Ciamis – 46215',
        ];
        $configs = \App\Models\KonfigurasiSistem::whereIn('key', array_keys($kopKeys))->get()->pluck('value', 'key');
        $kopData = [];
        foreach ($kopKeys as $key => $default) {
            $kopData[$key] = $configs->get($key) ?? $default;
        }

        $pdf = Pdf::loadView('pokja.pembimbing-dudi.export-pdf', array_merge(compact('mentors'), $kopData))
            ->setPaper('a4', 'landscape');
        $fileName = 'data-akun-pembimbing-dudi-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $query = \App\Models\PembimbingDudi::with(['user', 'dudi']);

        if (auth()->user()->konsentrasi_keahlian_id) {
            $userKonId = auth()->user()->konsentrasi_keahlian_id;
            $query->whereHas('dudi', function($q) use ($userKonId) {
                $q->where('konsentrasi_keahlian_id', $userKonId)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($userKonId) {
                      $sub->where('konsentrasi_keahlians.id', $userKonId);
                  });
            });
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $query->whereHas('dudi', function($q) use ($konsentrasiIds) {
                $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                      $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                  });
            });
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhereHas('dudi', function($d) use ($search) {
                          $d->where('nama', 'like', "%{$search}%");
                      });
                });
            }
        }

        $mentors = $query->orderBy('nama_lengkap')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'Nama Lengkap', 'Username', 'Email', 'Password', 'Perusahaan (DUDI)', 'Jabatan', 'No. HP'];
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        foreach ($mentors as $index => $mentor) {
            $row = $index + 2;
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $mentor->nama_lengkap);
            $sheet->setCellValue('C' . $row, $mentor->user->username);
            $sheet->setCellValue('D' . $row, $mentor->user->email);
            $sheet->setCellValue('E' . $row, 'pembimbing123');
            $sheet->setCellValue('F' . $row, $mentor->dudi->nama);
            $sheet->setCellValue('G' . $row, $mentor->jabatan);
            $sheet->setCellValueExplicit('H' . $row, $mentor->no_hp ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

        $totalCols = count($headers);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        $headerRange = 'A1:' . $lastColLetter . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        for ($col = 1; $col <= $totalCols; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $sheet->getRowDimension('1')->setRowHeight(25);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="data-akun-pembimbing-dudi-' . now()->format('Y-m-d') . '.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
