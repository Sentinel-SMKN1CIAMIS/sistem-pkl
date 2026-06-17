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

        $statusValue = $request->status === 'disetujui' ? 'disetujui_kaprog' : 'ditolak';

        $pengajuanPkl->update([
            'status' => $statusValue,
            'catatan' => $request->catatan,
            'acc_oleh' => auth()->id()
        ]);

        if ($statusValue === 'disetujui_kaprog') {
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
}
