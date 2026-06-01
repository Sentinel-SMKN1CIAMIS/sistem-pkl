<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class JurnalExportController extends Controller
{
    private function requirePkl()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa || !$siswa->dudi_id) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum memiliki tempat PKL yang disetujui. Silakan ajukan terlebih dahulu.');
        }
        return null;
    }

    public function export()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

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
