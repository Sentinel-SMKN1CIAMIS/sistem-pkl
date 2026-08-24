<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use App\Models\KonfigurasiSistem;
use App\Models\LaporanPkl;
use App\Models\Siswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function index()
    {
        $pembimbing = auth()->user()->pembimbingSekolah;
        
        $laporans = LaporanPkl::with('siswa')
            ->whereHas('siswa', function ($query) use ($pembimbing) {
                $query->where('pembimbing_sekolah_id', $pembimbing->id)
                      ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id);
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('pembimbing-sekolah.laporan.index', compact('laporans'));
    }

    public function export(LaporanPkl $laporan)
    {
        $pembimbing = auth()->user()->pembimbingSekolah;
        $siswa = $laporan->siswa;
        
        if ($siswa->pembimbing_sekolah_id !== $pembimbing->id && $siswa->pembimbing_sekolah_umum_id !== $pembimbing->id) {
            abort(403);
        }

        $siswa->load(['dudi', 'pembimbingSekolah', 'pembimbingDudi', 'konsentrasiKeahlian.programKeahlian', 'pengajuanPkl']);

        $kopKeys = [
            'report_kop_baris_1' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT',
            'report_kop_baris_2' => 'DINAS PENDIDIKAN',
            'report_kop_baris_3' => 'CABANG DINAS PENDIDIKAN WILAYAH XIII',
            'report_kop_baris_4' => 'SMK NEGERI 1 CIAMIS',
            'report_kop_baris_5' => 'Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204',
            'report_kop_baris_6' => 'Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net',
            'report_kop_baris_7' => 'Ciamis – 46215',
        ];
        $configs = KonfigurasiSistem::whereIn('key', array_keys($kopKeys))->pluck('value', 'key');
        $kopData = [];
        foreach ($kopKeys as $key => $default) {
            $kopData[$key] = $configs->get($key) ?? $default;
        }

        // Cari data Kaprog berdasarkan Program Keahlian atau Konsentrasi Keahlian Siswa
        $programKeahlianId = $siswa->konsentrasiKeahlian?->program_keahlian_id;
        $kaprog = null;
        if ($programKeahlianId) {
            $kaprog = User::where('role', 'kaprog')
                ->where('program_keahlian_id', $programKeahlianId)
                ->first();
        }
        if (!$kaprog && $siswa->konsentrasi_keahlian_id) {
            $kaprog = User::where('role', 'kaprog')
                ->where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)
                ->first();
        }

        $sharedTahun = View::shared('tahunAjaranActive');
        $tahunAjaran = ($sharedTahun && $sharedTahun !== '-') 
            ? $sharedTahun 
            : ($siswa->tahun_ajaran ?: (date('Y') . '/' . (date('Y') + 1)));

        $pdf = Pdf::loadView('siswa.laporan.export_pdf', array_merge([
            'siswa' => $siswa,
            'laporan' => $laporan,
            'kaprog' => $kaprog,
            'tahunAjaran' => $tahunAjaran,
        ], $kopData))->setPaper('a4', 'portrait');

        $safeName = Str::slug($siswa->nama_lengkap);
        return $pdf->download('Laporan-Akhir-PKL-' . $siswa->nis . '-' . $safeName . '.pdf');
    }

    public function update(Request $request, LaporanPkl $laporan)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // Ensure the laporan belongs to a student guided by this teacher
        $pembimbing = auth()->user()->pembimbingSekolah;
        if ($laporan->siswa->pembimbing_sekolah_id !== $pembimbing->id && $laporan->siswa->pembimbing_sekolah_umum_id !== $pembimbing->id) {
            abort(403);
        }

        $laporan->update([
            'status' => $request->status
        ]);

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        return redirect()->route('pembimbing_sekolah.laporan.index')->with('success', "Laporan berhasil $statusText.");
    }
}
