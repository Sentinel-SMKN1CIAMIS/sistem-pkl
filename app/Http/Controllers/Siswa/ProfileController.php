<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;
        return view('siswa.profile', compact('siswa'));
    }

    public function update(Request $request)
    {
        $siswa = auth()->user()->siswa;
        
        $request->validate([
            'pembimbing_dudi_nama' => 'nullable|string|max:255',
            'pembimbing_dudi_jabatan' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $siswa->update($request->only(['pembimbing_dudi_nama', 'pembimbing_dudi_jabatan', 'no_hp', 'alamat']));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
