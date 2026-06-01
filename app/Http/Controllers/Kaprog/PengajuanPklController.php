<?php

namespace App\Http\Controllers\Kaprog;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Models\Dudi;
use Illuminate\Http\Request;

class PengajuanPklController extends Controller
{
    public function index()
    {
        // Kaprog sees all pengajuan PKL
        $pengajuans = PengajuanPkl::with('siswa', 'dudi')->latest()->paginate(10);

        return view('kaprog.pengajuan-pkl.index', compact('pengajuans'));
    }

    public function update(Request $request, PengajuanPkl $pengajuanPkl)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $teacher = auth()->user()->pembimbingSekolah;

        $pengajuanPkl->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
            'acc_oleh' => auth()->id()
        ]);

        if ($request->status === 'disetujui') {
            if ($pengajuanPkl->dudi_id) {
                // If student selected an existing DUDI
                $dudi = Dudi::find($pengajuanPkl->dudi_id);
            } else {
                // Find or create DUDI if manually inputted
                $dudi = Dudi::firstOrCreate(
                    ['nama' => $pengajuanPkl->nama_perusahaan],
                    [
                        'nama_pimpinan' => $pengajuanPkl->pimpinan,
                        'alamat' => $pengajuanPkl->alamat,
                        'kota' => $pengajuanPkl->kota,
                        'no_telepon' => $pengajuanPkl->no_telp,
                        'konsentrasi_keahlian_id' => $pengajuanPkl->siswa->konsentrasi_keahlian_id,
                        'is_active' => true,
                    ]
                );
                
                // Update pengajuan with the new DUDI ID
                $pengajuanPkl->update(['dudi_id' => $dudi->id]);
            }

            if ($dudi) {
                // Assign DUDI to Siswa
                $pengajuanPkl->siswa->update([
                    'dudi_id' => $dudi->id,
                    'pembimbing_dudi_id' => $pengajuanPkl->pembimbing_dudi_id
                ]);

                // Notify Pokja that a student needs placement mapping
                $pokjas = \App\Models\User::where('role', 'pokja')->get();
                foreach ($pokjas as $pokja) {
                    \App\Models\Notifikasi::create([
                        'to_user_id' => $pokja->id,
                        'judul'      => 'Penempatan Baru (Butuh Pemetaan)',
                        'pesan'      => "Siswa {$pengajuanPkl->siswa->nama_lengkap} telah disetujui di {$dudi->nama}. Silakan lakukan pemetaan pembimbing.",
                        'link'       => route('pokja.pemetaan.index'),
                        'is_read'    => false,
                    ]);
                }

                // Notify Student
                \App\Models\Notifikasi::create([
                    'to_user_id' => $pengajuanPkl->siswa->user_id,
                    'judul'      => 'Pengajuan PKL Disetujui',
                    'pesan'      => "Pengajuan tempat PKL Anda di {$dudi->nama} telah disetujui oleh Kepala Program.",
                    'link'       => route('siswa.pengajuan_pkl.status'),
                    'is_read'    => false,
                ]);
            }
        } else {
            // Notify Student of rejection
            \App\Models\Notifikasi::create([
                'to_user_id' => $pengajuanPkl->siswa->user_id,
                'judul'      => 'Pengajuan PKL Ditolak',
                'pesan'      => "Pengajuan tempat PKL Anda di {$pengajuanPkl->nama_perusahaan} ditolak. Alasan: " . ($request->catatan ?? 'Tidak ada catatan.'),
                'link'       => route('siswa.pengajuan_pkl.status'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
