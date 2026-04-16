<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class JurnalExportController extends Controller
{
    public function export()
    {
        $siswa = auth()->user()->siswa;
        $jurnals = Jurnal::where('siswa_id', $siswa->id)
            ->where('status', 'valid')
            ->orderBy('tanggal', 'asc')
            ->get();

        $pdf = Pdf::loadView('siswa.jurnal.export', compact('siswa', 'jurnals'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Jurnal-PKL-' . $siswa->nis . '.pdf');
    }
}
