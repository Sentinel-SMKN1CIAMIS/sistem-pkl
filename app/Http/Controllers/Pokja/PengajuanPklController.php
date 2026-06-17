<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Models\Dudi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class PengajuanPklController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tampilkan pengajuan yang berstatus 'disetujui_kaprog'
        // Namun kita juga tampilkan riwayat pengajuan 'disetujui' dan 'ditolak' agar Pokja bisa melihat riwayatnya
        $pengajuans = PengajuanPkl::with('siswa', 'dudi')
            ->orderByRaw("CASE WHEN status = 'disetujui_kaprog' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(10);

        return view('pokja.pengajuan-pkl.index', compact('pengajuans'));
    }

    /**
     * Validate/Approve student's PKL proposal by Pokja
     */
    public function validasi(Request $request, PengajuanPkl $pengajuanPkl)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $statusValue = $request->status;

        $pengajuanPkl->update([
            'status' => $statusValue,
            'catatan' => $request->catatan,
        ]);

        if ($statusValue === 'disetujui') {
            if ($pengajuanPkl->dudi_id) {
                // Jika siswa memilih DUDI yang sudah ada
                $dudi = Dudi::find($pengajuanPkl->dudi_id);
            } else {
                // Cari atau buat DUDI baru jika di-input manual oleh siswa
                $dudi = Dudi::firstOrCreate(
                    ['nama' => $pengajuanPkl->nama_perusahaan],
                    [
                        'nama_pimpinan' => $pengajuanPkl->pimpinan,
                        'alamat' => $pengajuanPkl->alamat ?? '-',
                        'kota' => $pengajuanPkl->kota,
                        'no_telepon' => $pengajuanPkl->no_telp,
                        'konsentrasi_keahlian_id' => $pengajuanPkl->siswa->konsentrasi_keahlian_id,
                        'is_active' => true,
                    ]
                );
                
                // Update pengajuan dengan DUDI ID yang baru dibuat
                $pengajuanPkl->update(['dudi_id' => $dudi->id]);
            }

            if ($dudi) {
                // Assign DUDI & pembimbing dudi ke Siswa
                $pengajuanPkl->siswa->update([
                    'dudi_id' => $dudi->id,
                    'pembimbing_dudi_id' => $pengajuanPkl->pembimbing_dudi_id,
                    'status_pkl' => 'belum_mulai', // Siap dipetakan pembimbing sekolah
                ]);

                // Buat notifikasi untuk semua Pokja agar segera memetakan pembimbing sekolah
                $pokjas = \App\Models\User::where('role', 'pokja')->get();
                foreach ($pokjas as $pokja) {
                    Notifikasi::create([
                        'to_user_id' => $pokja->id,
                        'judul'      => 'Penempatan Baru (Butuh Pemetaan)',
                        'pesan'      => "Siswa {$pengajuanPkl->siswa->nama_lengkap} telah disetujui di {$dudi->nama}. Silakan lakukan pemetaan pembimbing.",
                        'link'       => route('pokja.pemetaan.index'),
                        'is_read'    => false,
                    ]);
                }

                // Notify Student
                Notifikasi::create([
                    'to_user_id' => $pengajuanPkl->siswa->user_id,
                    'judul'      => 'Pengajuan PKL Disetujui Pokja',
                    'pesan'      => "Pengajuan tempat PKL Anda di {$dudi->nama} telah disetujui oleh Pokja. Silakan unduh/cetak Surat Pengantar Anda.",
                    'link'       => route('siswa.pengajuan_pkl.status'),
                    'is_read'    => false,
                ]);
            }
        } else {
            // Notify Student of rejection by Pokja
            Notifikasi::create([
                'to_user_id' => $pengajuanPkl->siswa->user_id,
                'judul'      => 'Pengajuan PKL Ditolak Pokja',
                'pesan'      => "Pengajuan tempat PKL Anda di {$pengajuanPkl->nama_perusahaan} ditolak oleh Pokja. Alasan: " . ($request->catatan ?? 'Tidak ada catatan.'),
                'link'       => route('siswa.pengajuan_pkl.status'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Validasi pengajuan PKL berhasil diperbarui.');
    }
}
