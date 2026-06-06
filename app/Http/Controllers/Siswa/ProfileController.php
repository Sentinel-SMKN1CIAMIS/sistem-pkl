<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Zona;
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
            'unit_pekerjaan' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $siswa->update($request->only(['pembimbing_dudi_nama', 'pembimbing_dudi_jabatan', 'unit_pekerjaan', 'no_hp', 'alamat']));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateLokasiDudi(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $siswa = auth()->user()->siswa;

        if (!$siswa->dudi) {
            return response()->json(['error' => 'Anda belum ditugaskan ke DUDI manapun.'], 422);
        }

        $dudi = $siswa->dudi;
        $dudi->latitude = $request->latitude;
        $dudi->longitude = $request->longitude;

        // Auto-detect zona
        $zona = Zona::detectZona($request->latitude, $request->longitude);
        $dudi->zona_id = $zona ? $zona->id : null;

        $dudi->save();

        return response()->json([
            'message' => 'Lokasi DUDI berhasil diperbarui.',
            'zona' => $zona ? $zona->nama : null,
        ]);
    }
}
