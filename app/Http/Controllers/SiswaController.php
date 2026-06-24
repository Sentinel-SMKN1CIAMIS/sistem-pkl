<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Siswa::with(['user', 'konsentrasiKeahlian', 'dudi', 'pembimbingSekolah', 'pembimbingSekolahUmum']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->konsentrasi_keahlian_id) {
            $query->where('konsentrasi_keahlian_id' . '', $user->konsentrasi_keahlian_id);
        } elseif ($user->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id' . '', $user->program_keahlian_id)->pluck('id');
            $query->whereIn('konsentrasi_keahlian_id' . '', $konsentrasiIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap' . '', 'like', "%{$search}%")
                  ->orWhere('nis' . '', 'like', "%{$search}%");
            });
        }

        if ($request->filled('konsentrasi')) {
            $query->where('konsentrasi_keahlian_id' . '', $request->konsentrasi);
        }

        // Sorting Logic
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name_asc':
                    $query->orderBy('nama_lengkap' . '', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('nama_lengkap' . '', 'desc');
                    break;
                case 'nis_asc':
                    $query->orderBy('nis' . '', 'asc');
                    break;
                case 'nis_desc':
                    $query->orderBy('nis' . '', 'desc');
                    break;
                case 'kelas_asc':
                    $query->orderBy('kelas' . '', 'asc');
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $students = $query->paginate(10)->withQueryString();
        $concentrations = $user->getFilteredKonsentrasi();
        
        return view('pokja.siswa.index', compact('students', 'concentrations'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $concentrations = $user->getFilteredKonsentrasi();
        $dudis = \App\Models\Dudi::all();
        $pembimbingSekolah = \App\Models\PembimbingSekolah::all();
        $pembimbingDudi = \App\Models\PembimbingDudi::all();

        return view('pokja.siswa.create', compact('concentrations', 'dudis', 'pembimbingSekolah', 'pembimbingDudi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'kelas' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tahun_ajaran' => 'required|string',
        ]);

        try {
            // Wrap in database transaction for atomicity
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Create User first
            $user = \App\Models\User::create([
                'name' => $request->nama_lengkap,
                'username' => $request->nis,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'siswa',
            ]);

            // Create Siswa profile
            \App\Models\Siswa::create(array_merge($request->all(), ['user_id' => $user->id]));

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pokja.siswa.index')
                ->with('success', 'Data siswa dan akun berhasil dibuat.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            return back()->with('error', 'Gagal menambahkan data siswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(\App\Models\Siswa $siswa)
    {
        return view('pokja.siswa.show', compact('siswa'));
    }

    public function edit(\App\Models\Siswa $siswa)
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $dudis = \App\Models\Dudi::all();
        $pembimbingSekolah = \App\Models\PembimbingSekolah::all();
        $pembimbingDudi = \App\Models\PembimbingDudi::all();

        return view('pokja.siswa.edit', compact('siswa', 'concentrations', 'dudis', 'pembimbingSekolah', 'pembimbingDudi'));
    }

    public function update(Request $request, \App\Models\Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:255',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'kelas' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tahun_ajaran' => 'required|string',
        ]);

        $oldPembimbingId = $siswa->pembimbing_sekolah_id;
        $oldPembimbingUmumId = $siswa->pembimbing_sekolah_umum_id;
        $oldStatusPkl = $siswa->status_pkl;

        $data = $request->all();

        // Jika status_pkl diubah menjadi 'dibatalkan'
        if (isset($data['status_pkl']) && $data['status_pkl'] === 'dibatalkan') {
            $data['dudi_id'] = null;
            $data['pembimbing_sekolah_id'] = null;
            $data['pembimbing_sekolah_umum_id'] = null;
            $data['pembimbing_dudi_id'] = null;

            // Handle pengajuan tempat PKL lama: reject dan hapus bukti balasan
            $pengajuan = $siswa->pengajuanPkl;
            if ($pengajuan) {
                if ($pengajuan->bukti_balasan) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuan->bukti_balasan)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($pengajuan->bukti_balasan);
                    }
                }
                $pengajuan->update([
                    'status' => 'ditolak',
                    'catatan' => 'PKL dibatalkan oleh Pokja.',
                    'bukti_balasan' => null,
                ]);
            }
        }

        $siswa->update($data);
        
        // Update user name if changed
        $siswa->user->update(['name' => $request->nama_lengkap, 'username' => $request->nis]);

        // Kirim notifikasi jika Pembimbing Sekolah Kejuruan ditugaskan/berubah
        if ($siswa->pembimbing_sekolah_id && $siswa->pembimbing_sekolah_id != $oldPembimbingId) {
            $pembimbing = \App\Models\PembimbingSekolah::find($siswa->pembimbing_sekolah_id);
            if ($pembimbing) {
                // Notifikasi untuk Guru Pembimbing
                \App\Models\Notifikasi::create([
                    'to_user_id' => $pembimbing->user_id,
                    'judul'      => 'Penugasan Bimbingan Baru',
                    'pesan'      => "Anda telah ditugaskan sebagai Pembimbing Sekolah (Kejuruan) untuk siswa {$siswa->nama_lengkap} (NIS: {$siswa->nis}).",
                    'link'       => route('pembimbing_sekolah.siswa.index'),
                    'is_read'    => false,
                ]);

                // Notifikasi untuk Siswa
                \App\Models\Notifikasi::create([
                    'to_user_id' => $siswa->user_id,
                    'judul'      => 'Pembimbing Sekolah (Kejuruan) Ditugaskan',
                    'pesan'      => "Anda telah dibimbing oleh Guru Pembimbing Sekolah (Kejuruan): {$pembimbing->nama_lengkap}.",
                    'link'       => route('dashboard'),
                    'is_read'    => false,
                ]);
            }
        }

        // Kirim notifikasi jika Pembimbing Sekolah Umum ditugaskan/berubah
        if ($siswa->pembimbing_sekolah_umum_id && $siswa->pembimbing_sekolah_umum_id != $oldPembimbingUmumId) {
            $pembimbingUmum = \App\Models\PembimbingSekolah::find($siswa->pembimbing_sekolah_umum_id);
            if ($pembimbingUmum) {
                // Notifikasi untuk Guru Pembimbing Umum
                \App\Models\Notifikasi::create([
                    'to_user_id' => $pembimbingUmum->user_id,
                    'judul'      => 'Penugasan Bimbingan Baru',
                    'pesan'      => "Anda telah ditugaskan sebagai Pembimbing Sekolah (Umum) untuk siswa {$siswa->nama_lengkap} (NIS: {$siswa->nis}).",
                    'link'       => route('pembimbing_sekolah.siswa.index'),
                    'is_read'    => false,
                ]);

                // Notifikasi untuk Siswa
                \App\Models\Notifikasi::create([
                    'to_user_id' => $siswa->user_id,
                    'judul'      => 'Pembimbing Sekolah (Umum) Ditugaskan',
                    'pesan'      => "Anda telah dibimbing oleh Guru Pembimbing Sekolah (Umum): {$pembimbingUmum->nama_lengkap}.",
                    'link'       => route('dashboard'),
                    'is_read'    => false,
                ]);
            }
        }

        // Kirim notifikasi jika status PKL dibatalkan
        if (isset($data['status_pkl']) && $data['status_pkl'] === 'dibatalkan' && $oldStatusPkl !== 'dibatalkan') {
            \App\Models\Notifikasi::create([
                'to_user_id' => $siswa->user_id,
                'judul'      => 'PKL Dibatalkan oleh Pokja',
                'pesan'      => 'Status PKL Anda telah dibatalkan oleh Pokja. Silakan ajukan ulang tempat PKL baru.',
                'link'       => route('siswa.pengajuan_pkl.status'),
                'is_read'    => false,
            ]);
        }

        return redirect()->route('pokja.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(\App\Models\Siswa $siswa)
    {
        $user = $siswa->user;
        $siswa->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('pokja.siswa.index')
            ->with('success', 'Data siswa dan akun berhasil dihapus.');
    }
}
