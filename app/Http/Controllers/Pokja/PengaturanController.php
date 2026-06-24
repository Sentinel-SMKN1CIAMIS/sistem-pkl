<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\KonfigurasiSistem;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function sertifikat()
    {
        $template = KonfigurasiSistem::where('key', 'template_sertifikat')->value('value') ?? "Diberikan kepada [NAMA_SISWA] atas keberhasilannya menyelesaikan Praktik Kerja Lapangan di [NAMA_DUDI] pada tanggal [TANGGAL_AWAL] s/d [TANGGAL_AKHIR].";
        
        return view('pokja.pengaturan.sertifikat', compact('template'));
    }

    public function updateSertifikat(Request $request)
    {
        $request->validate([
            'template' => 'required|string'
        ]);

        KonfigurasiSistem::updateOrCreate(
            ['key' => 'template_sertifikat'],
            ['value' => $request->template]
        );

        return back()->with('success', 'Template sertifikat berhasil diperbarui.');
    }

    public function suratPengantar()
    {
        $keys = [
            'surat_kop_baris_1' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT',
            'surat_kop_baris_2' => 'DINAS PENDIDIKAN',
            'surat_kop_baris_3' => 'CABANG DINAS PENDIDIKAN WILAYAH XIII',
            'surat_kop_baris_4' => 'SMK NEGERI 1 CIAMIS',
            'surat_kop_baris_5' => 'Jalan : Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204',
            'surat_kop_baris_6' => 'Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net',
            'surat_kop_baris_7' => 'Ciamis – 46215',
            'surat_nomor_format' => '421.5 / ............ / SMKN1.CMS / PKL / [TAHUN_SEKARANG]',
            'surat_isi_pembuka' => 'Dengan hormat, dalam rangka mempersiapkan tenaga kerja yang terampil dan profesional serta memenuhi tuntutan kurikulum Sekolah Menengah Kejuruan (SMK), siswa tingkat akhir diwajibkan untuk menempuh program Praktik Kerja Lapangan (PKL). Kegiatan ini bertujuan untuk menyelaraskan teori yang diperoleh di sekolah dengan praktik langsung di dunia kerja.',
            'surat_isi_tengah' => 'Berkaitan dengan hal tersebut, kami mengajukan permohonan agar siswa kami berikut ini diperkenankan melaksanakan Praktik Kerja Lapangan (PKL) pada instansi/perusahaan yang Bapak/Ibu pimpin:',
            'surat_isi_penutup' => 'Pelaksanaan Praktik Kerja Lapangan (PKL) ini direncanakan akan berlangsung pada Tahun Pelajaran [TAHUN_AJARAN]. Selama pelaksanaan PKL, siswa diwajibkan mematuhi segala tata tertib dan peraturan yang berlaku di perusahaan/instansi Bapak/Ibu.',
            'surat_isi_salam' => 'Besar harapan kami permohonan ini dapat dipertimbangkan dan dikabulkan. Atas bantuan, perhatian, serta kerja sama yang terjalin selama ini, kami mengucapkan terima kasih.',
            'surat_ttd_jabatan' => 'Ketua Pokja PKL SMKN 1 Ciamis',
            'surat_ttd_nama' => '......................................................',
            'surat_ttd_nip' => 'NIP. .................................................',
        ];

        $configs = KonfigurasiSistem::whereIn('key', array_keys($keys))->get()->pluck('value', 'key');

        $data = [];
        foreach ($keys as $key => $default) {
            $data[$key] = $configs->get($key) ?? $default;
        }

        return view('pokja.pengaturan.surat-pengantar', $data);
    }

    public function updateSuratPengantar(Request $request)
    {
        $keys = [
            'surat_kop_baris_1',
            'surat_kop_baris_2',
            'surat_kop_baris_3',
            'surat_kop_baris_4',
            'surat_kop_baris_5',
            'surat_kop_baris_6',
            'surat_kop_baris_7',
            'surat_nomor_format',
            'surat_isi_pembuka',
            'surat_isi_tengah',
            'surat_isi_penutup',
            'surat_isi_salam',
            'surat_ttd_jabatan',
            'surat_ttd_nama',
            'surat_ttd_nip',
        ];

        $rules = [];
        foreach ($keys as $key) {
            $rules[$key] = 'required|string';
        }

        $request->validate($rules);

        foreach ($keys as $key) {
            KonfigurasiSistem::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        return back()->with('success', 'Format Surat Pengantar PKL berhasil diperbarui.');
    }
}
