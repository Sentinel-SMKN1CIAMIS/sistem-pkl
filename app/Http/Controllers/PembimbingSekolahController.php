<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembimbingSekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = \App\Models\PembimbingSekolah::with(['user', 'konsentrasiKeahlian'])->latest()->paginate(10);
        return view('pokja.pembimbing-sekolah.index', compact('teachers'));
    }

    public function create()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $existingClasses = \App\Models\Siswa::distinct('kelas')->pluck('kelas')->filter()->values();
        return view('pokja.pembimbing-sekolah.create', compact('concentrations', 'existingClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|unique:pembimbing_sekolahs,nip',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'tipe' => 'required|in:normatif,adaptif,produktif',
            'no_hp' => 'nullable|string',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'pembimbing_sekolah',
        ]);

        $pembimbing = \App\Models\PembimbingSekolah::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'konsentrasi_keahlian_id' => $request->konsentrasi_keahlian_id,
            'tipe' => $request->tipe,
            'no_hp' => $request->no_hp,
            'mapel_cp' => $request->mapel_cp,
        ]);

        if ($request->has('kelas') && is_array($request->kelas)) {
            foreach ($request->kelas as $kls) {
                \App\Models\KelasPembimbing::create([
                    'pembimbing_sekolah_id' => $pembimbing->id,
                    'kelas' => $kls
                ]);
            }
        }

        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', 'Pembimbing sekolah berhasil ditambahkan.');
    }

    public function show(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $students = $pembimbing_sekolah->siswa()->with(['konsentrasiKeahlian', 'dudi'])->get();
        return view('pokja.pembimbing-sekolah.show', compact('pembimbing_sekolah', 'students'));
    }

    public function edit(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $existingClasses = \App\Models\Siswa::distinct('kelas')->pluck('kelas')->filter()->values();
        $currentClasses = $pembimbing_sekolah->kelasDiajar()->pluck('kelas')->toArray();
        return view('pokja.pembimbing-sekolah.edit', compact('pembimbing_sekolah', 'concentrations', 'existingClasses', 'currentClasses'));
    }

    public function update(Request $request, \App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $request->validate([
            'nip' => 'nullable|unique:pembimbing_sekolahs,nip,' . $pembimbing_sekolah->id,
            'nama_lengkap' => 'required|string|max:255',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'tipe' => 'required|in:normatif,adaptif,produktif',
            'no_hp' => 'nullable|string',
        ]);

        $pembimbing_sekolah->update([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'konsentrasi_keahlian_id' => $request->konsentrasi_keahlian_id,
            'tipe' => $request->tipe,
            'no_hp' => $request->no_hp,
            'mapel_cp' => $request->mapel_cp,
        ]);
        
        $pembimbing_sekolah->user->update(['name' => $request->nama_lengkap]);

        // Sync classes
        \App\Models\KelasPembimbing::where('pembimbing_sekolah_id', $pembimbing_sekolah->id)->delete();
        if ($request->has('kelas') && is_array($request->kelas)) {
            foreach ($request->kelas as $kls) {
                \App\Models\KelasPembimbing::create([
                    'pembimbing_sekolah_id' => $pembimbing_sekolah->id,
                    'kelas' => $kls
                ]);
            }
        }

        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', 'Data pembimbing sekolah berhasil diperbarui.');
    }

    public function destroy(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $pembimbing_sekolah->delete();
        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', 'Data pembimbing sekolah berhasil dihapus.');
    }
}
