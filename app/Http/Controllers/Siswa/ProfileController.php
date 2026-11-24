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
        $updateFields = ['unit_pekerjaan', 'no_hp'];
        if (!$siswa->pembimbing_dudi_id) {
            $updateFields = array_merge($updateFields, ['pembimbing_dudi_nama', 'pembimbing_dudi_jabatan', 'pembimbing_dudi_no_hp']);
        }
        
        $siswaData = $request->only($updateFields);
        if (!$siswa->dudi && !$siswa->pengajuanPkl) {
            $siswaData['alamat'] = $request->alamat;
        } elseif ($request->filled('alamat')) {
            $siswaData['alamat'] = $request->alamat;
        }
        $siswa->update($siswaData);

        // 2. Synchronize address to DUDI or Pengajuan (only if non-empty, as dudis.alamat cannot be null)
        if ($request->filled('alamat')) {
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

    public function updateReminderSettings(Request $request)
    {
        $request->validate([
            'jam_notif_masuk' => ['required', 'string', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'jam_notif_pulang' => ['nullable', 'string', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'jam_notif_jurnal' => ['nullable', 'string', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'hari_libur_rutin' => ['nullable', 'array'],
            'hari_libur_rutin.*' => ['integer', 'between:1,7'],
        ], [
            'jam_notif_masuk.required' => 'Jam pengingat masuk wajib diisi.',
            'jam_notif_masuk.regex' => 'Format jam pengingat masuk harus HH:MM (contoh: 07:30).',
            'jam_notif_pulang.regex' => 'Format jam pengingat pulang harus HH:MM (contoh: 16:00).',
            'jam_notif_jurnal.regex' => 'Format jam pengingat jurnal harus HH:MM (contoh: 19:30).',
            'hari_libur_rutin.*.between' => 'Hari libur rutin harus di antara Senin (1) sampai Minggu (7).',
        ]);

        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
            }
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $offDays = collect($request->input('hari_libur_rutin', []))
            ->map(fn($d) => (int) $d)
            ->filter(fn($d) => $d >= 1 && $d <= 7)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $siswa->update([
            'jam_notif_masuk' => $request->jam_notif_masuk,
            'jam_notif_pulang' => $request->jam_notif_pulang ?: '16:00',
            'jam_notif_jurnal' => $request->jam_notif_jurnal ?: '19:30',
            'hari_libur_rutin' => $offDays,
            'reminder_settings_configured' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal alarm & hari libur rutin berhasil disimpan.',
                'data' => [
                    'jam_notif_masuk' => $siswa->jam_notif_masuk,
                    'jam_notif_pulang' => $siswa->jam_notif_pulang,
                    'jam_notif_jurnal' => $siswa->jam_notif_jurnal,
                    'hari_libur_rutin' => $siswa->hari_libur_rutin,
                ],
            ]);
        }

        return back()->with('success', 'Jadwal alarm & hari libur rutin berhasil disimpan.');
    }
}
