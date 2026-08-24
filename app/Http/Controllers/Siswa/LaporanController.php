<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LaporanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\KonfigurasiSistem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    private function requirePkl()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa || !$siswa->dudi_id) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum dapat mengakses menu ini. Pastikan pengajuan PKL telah disetujui.');
        }

        if ($siswa->status_pkl === 'belum_mulai') {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Tempat PKL Anda sudah disetujui, namun Anda belum bisa mengakses menu ini karena menunggu Tim Pokja memetakan Guru Pembimbing Sekolah.');
        }

        if (!in_array($siswa->status_pkl, ['sedang_pkl', 'selesai'])) {
            return redirect()->route('siswa.pengajuan_pkl.status')
                ->with('error', 'Anda belum dapat mengakses menu ini. Pastikan Surat Pengantar telah di-ACC dan DUDI telah membalas (menerima) Anda.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = Auth::user()->siswa;
        $laporan = LaporanPkl::where('siswa_id', $siswa->id)->first();
        return view('siswa.laporan.index', compact('laporan'));
    }

    public function export()
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $siswa = Auth::user()->siswa;
        $siswa->load(['dudi', 'pembimbingSekolah', 'pembimbingDudi', 'konsentrasiKeahlian.programKeahlian', 'pengajuanPkl']);

        $laporan = LaporanPkl::where('siswa_id', $siswa->id)->first();
        if (!$laporan) {
            return redirect()->route('siswa.laporan.index')->with('error', 'Anda belum mengisi atau mengirim Laporan Akhir PKL.');
        }

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

    public function store(Request $request)
    {
        if ($redirect = $this->requirePkl()) return $redirect;

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link_media_sosial' => 'nullable|array|max:5',
            'link_media_sosial.*' => 'nullable|url',
        ]);

        $siswa = auth()->user()->siswa;
        
        // Filter empty links
        $links = $request->link_media_sosial ? array_filter($request->link_media_sosial) : null;
        
        LaporanPkl::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'link_media_sosial' => empty($links) ? null : array_values($links),
                'status' => 'submitted',
                'submitted_at' => Carbon::now()
            ]
        );

        return redirect()->route('siswa.laporan.index')->with('success', 'Laporan berhasil diunggah.');
    }
}
