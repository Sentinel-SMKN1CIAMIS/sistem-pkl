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
        $user = auth()->user();
        $query = PengajuanPkl::with('siswa', 'dudi');
        
        if ($user->role === 'kaprog') {
            $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();
            $query->whereHas('siswa', function($q) use ($allowedIds) {
                $q->whereIn('konsentrasi_keahlian_id', $allowedIds);
            });
        }
        
        $pengajuans = $query->latest()->paginate(10);

        return view('kaprog.pengajuan-pkl.index', compact('pengajuans'));
    }

    public function update(Request $request, PengajuanPkl $pengajuanPkl)
    {
        $user = auth()->user();

        // Re-enabled proper authorization check
        if ($user->role === 'kaprog') {
            $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();
            if (!in_array($pengajuanPkl->siswa->konsentrasi_keahlian_id, $allowedIds)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        if ($request->status === 'disetujui') {
            if ($pengajuanPkl->dudi_id) {
                // Jika siswa memilih DUDI yang sudah ada, persetujuan Kaprog bersifat final (langsung disetujui)
                $statusValue = 'disetujui';
            } else {
                // Jika siswa menginput DUDI baru secara manual, perlu validasi Pokja
                $statusValue = 'disetujui_kaprog';
            }
        } else {
            $statusValue = 'ditolak';
        }

        $pengajuanPkl->update([
            'status' => $statusValue,
            'catatan' => $request->catatan,
            'acc_oleh' => auth()->id()
        ]);

        if ($statusValue === 'disetujui') {
            $dudi = Dudi::find($pengajuanPkl->dudi_id);
            if ($dudi) {
                // Assign DUDI & pembimbing dudi ke Siswa
                $pengajuanPkl->siswa->update([
                    'dudi_id' => $dudi->id,
                    'pembimbing_dudi_id' => $pengajuanPkl->pembimbing_dudi_id,
                    'status_pkl' => 'belum_mulai', // Siap dipetakan pembimbing sekolah oleh Pokja
                ]);

                // Buat notifikasi untuk semua Pokja agar segera memetakan pembimbing sekolah
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
                    'pesan'      => "Pengajuan tempat PKL Anda di {$dudi->nama} telah disetujui. Silakan unduh/cetak Surat Pengantar Anda.",
                    'link'       => route('siswa.pengajuan_pkl.status'),
                    'is_read'    => false,
                ]);
            }
        } elseif ($statusValue === 'disetujui_kaprog') {
            // Notify Pokja that a student needs validation
            $pokjas = \App\Models\User::where('role', 'pokja')->get();
            foreach ($pokjas as $pokja) {
                \App\Models\Notifikasi::create([
                    'to_user_id' => $pokja->id,
                    'judul'      => 'Pengajuan PKL Baru (Butuh Validasi)',
                    'pesan'      => "Pengajuan PKL siswa {$pengajuanPkl->siswa->nama_lengkap} di {$pengajuanPkl->nama_perusahaan} telah disetujui Kaprog dan memerlukan validasi Pokja.",
                    'link'       => route('pokja.pengajuan_pkl.index'),
                    'is_read'    => false,
                ]);
            }

            // Notify Student
            \App\Models\Notifikasi::create([
                'to_user_id' => $pengajuanPkl->siswa->user_id,
                'judul'      => 'Pengajuan PKL Disetujui Kaprog',
                'pesan'      => "Pengajuan tempat PKL Anda di {$pengajuanPkl->nama_perusahaan} telah disetujui oleh Kaprog dan sedang menunggu validasi Pokja.",
                'link'       => route('siswa.pengajuan_pkl.status'),
                'is_read'    => false,
            ]);
        } else {
            // Notify Student of rejection by Kaprog
            \App\Models\Notifikasi::create([
                'to_user_id' => $pengajuanPkl->siswa->user_id,
                'judul'      => 'Pengajuan PKL Ditolak Kaprog',
                'pesan'      => "Pengajuan tempat PKL Anda di {$pengajuanPkl->nama_perusahaan} ditolak oleh Kaprog. Alasan: " . ($request->catatan ?? 'Tidak ada catatan.'),
                'link'       => route('siswa.pengajuan_pkl.status'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Delete/Clean up student's PKL proposal by Kaprog (restricted to their department)
     */
    public function destroy(PengajuanPkl $pengajuanPkl)
    {
        $user = auth()->user();
        if ($user->role === 'kaprog') {
            $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();
            if (!in_array($pengajuanPkl->siswa->konsentrasi_keahlian_id, $allowedIds)) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Hapus berkas bukti balasan jika ada di storage
        if ($pengajuanPkl->bukti_balasan) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuanPkl->bukti_balasan)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengajuanPkl->bukti_balasan);
            }
        }

        $pengajuanPkl->delete();

        return back()->with('success', 'Pengajuan PKL berhasil dihapus.');
    }

    /**
     * Delete/Clear all students' PKL proposals in Kaprog's department
     */
    public function clearAll()
    {
        $user = auth()->user();
        $query = PengajuanPkl::query();

        if ($user->role === 'kaprog') {
            $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();
            $query->whereHas('siswa', function($q) use ($allowedIds) {
                $q->whereIn('konsentrasi_keahlian_id', $allowedIds);
            });
        }

        // Hapus berkas bukti balasan untuk semua pengajuan yang memilikinya
        $pengajuans = $query->whereNotNull('bukti_balasan')->get();
        foreach ($pengajuans as $pengajuan) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuan->bukti_balasan)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengajuan->bukti_balasan);
            }
        }

        // Hapus data pengajuan milik Kaprog
        if ($user->role === 'kaprog') {
            $allowedIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->toArray();
            PengajuanPkl::whereHas('siswa', function($q) use ($allowedIds) {
                $q->whereIn('konsentrasi_keahlian_id', $allowedIds);
            })->delete();
        } else {
            PengajuanPkl::query()->delete();
        }

        return back()->with('success', 'Semua data pengajuan PKL siswa di program keahlian Anda berhasil dihapus.');
    }
}
