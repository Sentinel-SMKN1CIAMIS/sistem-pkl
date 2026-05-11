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
        return view('pokja.pembimbing-sekolah.create', compact('concentrations'));
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

        \App\Models\PembimbingSekolah::create(array_merge($request->all(), ['user_id' => $user->id]));

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
        return view('pokja.pembimbing-sekolah.edit', compact('pembimbing_sekolah', 'concentrations'));
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

        $pembimbing_sekolah->update($request->all());
        $pembimbing_sekolah->user->update(['name' => $request->nama_lengkap]);

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
