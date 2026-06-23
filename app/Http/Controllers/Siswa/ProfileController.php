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
            'pembimbing_dudi_no_hp' => 'nullable|string|max:20',
            'unit_pekerjaan' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        // 1. Update siswa attributes
        $updateFields = ['unit_pekerjaan', 'no_hp', 'alamat'];
        if (!$siswa->pembimbing_dudi_id) {
            $updateFields = array_merge($updateFields, ['pembimbing_dudi_nama', 'pembimbing_dudi_jabatan', 'pembimbing_dudi_no_hp']);
        }
        $siswa->update($request->only($updateFields));

        // 2. Synchronize address to DUDI or Pengajuan
        if ($siswa->dudi) {
            $siswa->dudi->update(['alamat' => $request->alamat]);
        } elseif ($siswa->pengajuanPkl) {
            if ($siswa->pengajuanPkl->dudi_id) {
                if ($siswa->pengajuanPkl->dudi) {
                    $siswa->pengajuanPkl->dudi->update(['alamat' => $request->alamat]);
                }
            } else {
                $siswa->pengajuanPkl->update(['alamat' => $request->alamat]);
            }
        }

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
