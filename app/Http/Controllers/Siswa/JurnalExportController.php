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
        if (!$siswa || !$siswa->dudi_id || !in_array($siswa->status_pkl, ['sedang_pkl', 'selesai'])) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum dapat mengakses menu ini. Pastikan Surat Pengantar telah di-ACC dan DUDI telah membalas (menerima) Anda.');
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

    public function portofolio()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        
        // Group journals by Tujuan Pembelajaran (TP)
        $jurnalsByTp = Jurnal::where('siswa_id', $siswa->id)
            ->where('status', 'valid')
            ->whereNotNull('cp_id')
            ->with(['tujuanPembelajaran', 'kompetensi'])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('cp_id');

        $pdf = Pdf::loadView('siswa.jurnal.portofolio_pdf', compact('siswa', 'jurnalsByTp'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Portofolio-PKL-' . $siswa->nis . '.pdf');
    }

    public function sertifikat()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = auth()->user()->siswa;
        
        // Ensure student has completed PKL to get the certificate
        if ($siswa->status_pkl !== 'selesai') {
            return redirect()->route('siswa.jurnal.index')
                ->with('error', 'Sertifikat hanya dapat dicetak setelah status PKL Anda dinyatakan Selesai.');
        }

        $template = \App\Models\KonfigurasiSistem::where('key', 'template_sertifikat')->value('value') 
            ?? "Diberikan kepada [NAMA_SISWA] atas keberhasilannya menyelesaikan Praktik Kerja Lapangan di [NAMA_DUDI] pada tanggal [TANGGAL_AWAL] s/d [TANGGAL_AKHIR].";
            
        // Replace placeholders
        $text = str_replace(
            ['[NAMA_SISWA]', '[NIS]', '[NAMA_DUDI]', '[JURUSAN]', '[TANGGAL_AWAL]', '[TANGGAL_AKHIR]'],
            [
                $siswa->nama_lengkap, 
                $siswa->nis, 
                $siswa->dudi ? $siswa->dudi->nama : '-',
                $siswa->konsentrasiKeahlian ? $siswa->konsentrasiKeahlian->nama : '-',
                // Actually we don't have start/end dates in DB explicitly yet, let's just put something or check if PengajuanPkl has it. We will just put a placeholder for now.
                '-', 
                '-'
            ],
            $template
        );

        $pdf = Pdf::loadView('siswa.jurnal.sertifikat_pdf', compact('siswa', 'text'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Sertifikat_PKL_' . $siswa->nis . '.pdf');
    }
}
