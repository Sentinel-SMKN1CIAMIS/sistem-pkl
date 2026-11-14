<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use App\Models\Dudi;
use App\Models\PembimbingDudi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DudiMentorController extends Controller
{
    /**
     * Tampilkan daftar DUDI bimbingan dan mentor DUDI yang terdaftar.
     */
    public function index()
    {
        $user = Auth::user();
        $pembimbingSekolah = $user->pembimbingSekolah;

        if (!$pembimbingSekolah) {
            abort(403, 'Profil pembimbing sekolah tidak ditemukan.');
        }

        // Ambil DUDI dari siswa yang dibimbing oleh guru ini
        $dudiIds = Siswa::where(function($q) use ($pembimbingSekolah) {
            $q->where('pembimbing_sekolah_id', $pembimbingSekolah->id)
              ->orWhere('pembimbing_sekolah_umum_id', $pembimbingSekolah->id);
        })->whereNotNull('dudi_id')->pluck('dudi_id')->unique();

        $dudis = Dudi::whereIn('id', $dudiIds)
            ->with(['pembimbingDudi.user', 'siswa' => function($q) use ($pembimbingSekolah) {
                $q->where('pembimbing_sekolah_id', $pembimbingSekolah->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $pembimbingSekolah->id);
            }])
            ->get();

        return view('pembimbing-sekolah.dudi-mentor.index', compact('dudis'));
    }

    /**
     * Buat akun pembimbing DUDI baru oleh pembimbing sekolah.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pembimbingSekolah = $user->pembimbingSekolah;

        if (!$pembimbingSekolah) {
            abort(403, 'Profil pembimbing sekolah tidak ditemukan.');
        }

        $request->validate([
            'dudi_id' => 'required|exists:dudis,id',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => ['nullable', 'string', 'unique:users,username', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
        ]);

        // Verifikasi bahwa guru ini memang membimbing di DUDI tersebut
        $hasStudentsInDudi = Siswa::where(function($q) use ($pembimbingSekolah) {
            $q->where('pembimbing_sekolah_id', $pembimbingSekolah->id)
              ->orWhere('pembimbing_sekolah_umum_id', $pembimbingSekolah->id);
        })->where('dudi_id', $request->dudi_id)->exists();

        if (!$hasStudentsInDudi) {
            return back()->with('error', 'Anda tidak memiliki anak bimbingan di DUDI tersebut.');
        }

        $username = $request->username ?: (Str::slug($request->nama_lengkap, '.') . rand(10, 99));

        DB::transaction(function() use ($request, $username) {
            $userDudi = User::create([
                'name' => $request->nama_lengkap,
                'username' => $username,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'pembimbing_dudi',
                'is_active' => true,
                'force_password_change' => false,
            ]);

            $mentor = PembimbingDudi::create([
                'user_id' => $userDudi->id,
                'dudi_id' => $request->dudi_id,
                'nama_lengkap' => $request->nama_lengkap,
                'jabatan' => $request->jabatan ?? 'Pembimbing Lapangan',
                'no_hp' => $request->no_hp,
            ]);

            // Otomatis hubungkan ke siswa di DUDI tersebut yang belum memiliki pembimbing dudi
            Siswa::where('dudi_id', $request->dudi_id)
                ->whereNull('pembimbing_dudi_id')
                ->update([
                    'pembimbing_dudi_id' => $mentor->id,
                    'pembimbing_dudi_nama' => $mentor->nama_lengkap,
                ]);
        });

        return back()->with('success', "Akun pembimbing DUDI {$request->nama_lengkap} berhasil dibuat dan dihubungkan ke siswa bimbingan.");
    }
}
