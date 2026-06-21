<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Models\Dudi;
use Illuminate\Http\Request;

class PengajuanPklController extends Controller
{
    public function create()
    {
        $siswa = auth()->user()->siswa;

        // Jika sudah punya DUDI, tidak perlu mengajukan lagi
        if ($siswa->dudi_id) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah memiliki tempat PKL yang telah disetujui.');
        }

        // Jika sudah ada pengajuan yang menunggu, disetujui kaprog, atau disetujui final
        $existing = $siswa->pengajuanPkl;
        if ($existing && in_array($existing->status, ['menunggu', 'disetujui_kaprog', 'disetujui'])) {
            return redirect()->route('siswa.pengajuan_pkl.status');
        }

        // Ambil daftar DUDI yang relevan dengan konsentrasi keahlian siswa
        $dudis = Dudi::where('is_active', true)
            ->where(function ($q) use ($siswa) {
                $q->where('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id)
                  ->orWhereHas('konsentrasiKeahlians', function($sub) use ($siswa) {
                      $sub->where('konsentrasi_keahlians.id', $siswa->konsentrasi_keahlian_id);
                  });
            })->get();

        return view('siswa.pengajuan-pkl.create', compact('dudis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dudi_id'            => 'nullable|exists:dudis,id',
            'pembimbing_dudi_id' => 'nullable|exists:pembimbing_dudis,id',
            'nama_perusahaan'    => 'required|string|max:255',
            'pimpinan'           => 'nullable|string|max:255',
            'alamat'             => 'required_without:dudi_id|nullable|string|max:1000',
            'kota'               => 'required_without:dudi_id|nullable|string|max:100',
            'no_telp'            => 'required_without:dudi_id|nullable|string|max:30',
        ], [
            'alamat.required_without' => 'Alamat lengkap wajib diisi untuk tempat PKL baru.',
            'kota.required_without' => 'Kota wajib diisi untuk tempat PKL baru.',
            'no_telp.required_without' => 'No. telepon perusahaan wajib diisi untuk tempat PKL baru.',
        ]);

        $siswa = auth()->user()->siswa;

        if ($siswa->dudi_id) {
            return redirect()->route('dashboard');
        }

        // Hapus pengajuan ditolak sebelumnya dan buat yang baru
        $existing = $siswa->pengajuanPkl;
        if ($existing) {
            if ($existing->bukti_balasan) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($existing->bukti_balasan)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($existing->bukti_balasan);
                }
            }
            $existing->delete();
        }

        $pengajuan = PengajuanPkl::create([
            'siswa_id'           => $siswa->id,
            'dudi_id'            => $request->dudi_id,
            'pembimbing_dudi_id' => $request->pembimbing_dudi_id,
            'nama_perusahaan'    => $request->nama_perusahaan,
            'pimpinan'           => $request->pimpinan,
            'alamat'             => $request->alamat,
            'kota'               => $request->kota,
            'no_telp'            => $request->no_telp,
            'status'             => 'menunggu',
            'is_manual'          => empty($request->dudi_id),
        ]);

        // Kirim notifikasi ke Kaprog di program keahlian siswa
        $programKeahlianId = $siswa->konsentrasiKeahlian?->program_keahlian_id;
        $kaprogs = \App\Models\User::where('role', 'kaprog')
            ->when($programKeahlianId, function ($q) use ($programKeahlianId) {
                $q->where('program_keahlian_id', $programKeahlianId);
            })->get();

        foreach ($kaprogs as $kaprog) {
            \App\Models\Notifikasi::create([
                'to_user_id' => $kaprog->id,
                'judul'      => 'Pengajuan Tempat PKL Baru',
                'pesan'      => "Siswa {$siswa->nama_lengkap} mengajukan tempat PKL baru di {$pengajuan->nama_perusahaan}.",
                'link'       => route('kaprog.pengajuan_pkl.index'),
                'is_read'    => false,
            ]);
        }

        // Kirim notifikasi ke Pokja
        $pokjas = \App\Models\User::where('role', 'pokja')->get();
        foreach ($pokjas as $pokja) {
            \App\Models\Notifikasi::create([
                'to_user_id' => $pokja->id,
                'judul'      => 'Pengajuan Tempat PKL Baru',
                'pesan'      => "Siswa {$siswa->nama_lengkap} mengajukan tempat PKL baru di {$pengajuan->nama_perusahaan}.",
                'link'       => route('pokja.pengajuan_pkl.index'),
                'is_read'    => false,
            ]);
        }

        return redirect()->route('siswa.pengajuan_pkl.status')
            ->with('success', 'Pengajuan tempat PKL berhasil dikirim! Mohon tunggu konfirmasi dari Ketua Program Keahlian (Kaprog).');
    }

    public function getPembimbing(Request $request)
    {
        $dudi_id = $request->get('dudi_id');
        if (!$dudi_id) {
            return response()->json([]);
        }
        $pembimbing = \App\Models\PembimbingDudi::where('dudi_id', $dudi_id)->get();
        return response()->json($pembimbing);
    }

    public function status()
    {
        $siswa = auth()->user()->siswa;

        // Jika siswa sudah terpetakan pembimbing sekolahnya (sedang_pkl), redirect ke dashboard
        if ($siswa->status_pkl === 'sedang_pkl') {
            return redirect()->route('dashboard')->with('info', 'Anda sudah aktif melaksanakan PKL.');
        }

        $pengajuan = $siswa->pengajuanPkl;

        return view('siswa.pengajuan-pkl.status', compact('pengajuan'));
    }

    public function print()
    {
        $siswa = auth()->user()->siswa;
        $pengajuan = $siswa->pengajuanPkl;

        if (!$pengajuan || $pengajuan->status !== 'disetujui') {
            return redirect()->route('siswa.pengajuan_pkl.status')->with('error', 'Surat pengantar belum diterbitkan atau pengajuan Anda belum disetujui.');
        }

        // Ambil tahun ajaran aktif dari shared view variables
        $tahunAjaranActive = view()->shared('tahunAjaranActive') ?: '-';

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

        $configs = \App\Models\KonfigurasiSistem::whereIn('key', array_keys($keys))->get()->pluck('value', 'key');

        $compiled = [];
        $placeholders = [
            '[NAMA_SISWA]',
            '[NIS]',
            '[KELAS]',
            '[JURUSAN]',
            '[NAMA_PERUSAHAAN]',
            '[TAHUN_AJARAN]',
            '[TAHUN_SEKARANG]',
            '[TANGGAL_SEKARANG]'
        ];

        $replacements = [
            $siswa->nama_lengkap,
            $siswa->nis,
            $siswa->kelas,
            $siswa->konsentrasiKeahlian?->nama ?? '-',
            $pengajuan->nama_perusahaan,
            $tahunAjaranActive,
            date('Y'),
            now()->translatedFormat('d F Y')
        ];

        foreach ($keys as $key => $default) {
            $rawText = $configs->get($key) ?? $default;
            $compiled[$key] = str_replace($placeholders, $replacements, $rawText);
        }

        return view('siswa.pengajuan-pkl.print', array_merge([
            'siswa' => $siswa,
            'pengajuan' => $pengajuan,
            'tahunAjaranActive' => $tahunAjaranActive,
        ], $compiled));
    }

    public function uploadBukti(Request $request)
    {
        $siswa = auth()->user()->siswa;
        $pengajuan = $siswa->pengajuanPkl;

        if (!$pengajuan || $pengajuan->status !== 'disetujui') {
            return back()->with('error', 'Anda belum dapat mengunggah bukti penerimaan sebelum pengajuan disetujui Pokja.');
        }

        $request->validate([
            'bukti_balasan' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ], [
            'bukti_balasan.required' => 'Berkas bukti penerimaan wajib dipilih.',
            'bukti_balasan.file' => 'Berkas yang diunggah harus berupa file.',
            'bukti_balasan.mimes' => 'Format berkas hanya diperbolehkan PDF, PNG, JPG, atau JPEG.',
            'bukti_balasan.max' => 'Ukuran berkas maksimal adalah 2MB.',
        ]);

        try {
            // Hapus bukti lama jika ada
            if ($pengajuan->bukti_balasan) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuan->bukti_balasan)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($pengajuan->bukti_balasan);
                }
            }

            $file = $request->file('bukti_balasan');
            $fileName = 'bukti_' . $siswa->nis . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_balasan', $fileName, 'public');

            $pengajuan->update([
                'bukti_balasan' => $path,
            ]);

            // Kirim notifikasi ke Pokja
            $pokjas = \App\Models\User::where('role', 'pokja')->get();
            foreach ($pokjas as $pokja) {
                \App\Models\Notifikasi::create([
                    'to_user_id' => $pokja->id,
                    'judul'      => 'Bukti Penerimaan Perusahaan Baru',
                    'pesan'      => "Siswa {$siswa->nama_lengkap} telah mengunggah bukti penerimaan dari perusahaan {$pengajuan->nama_perusahaan}.",
                    'link'       => route('pokja.siswa.edit', $siswa->id),
                    'is_read'    => false,
                ]);
            }

            return back()->with('success', 'Bukti penerimaan perusahaan berhasil diunggah.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah bukti: ' . $e->getMessage());
        }
    }
}
