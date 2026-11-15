<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembimbingSekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\PembimbingSekolah::with(['user', 'konsentrasiKeahlian']);

        $search = $request->input('search');
        $tipe = $request->input('tipe', 'semua');
        $konsentrasi_id = $request->input('konsentrasi_keahlian_id', 'semua');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($tipe !== 'semua') {
            $query->where('tipe', $tipe);
        }

        if ($konsentrasi_id !== 'semua') {
            $query->where('konsentrasi_keahlian_id', $konsentrasi_id);
        }

        if (auth()->user()->konsentrasi_keahlian_id) {
            $query->where('konsentrasi_keahlian_id', auth()->user()->konsentrasi_keahlian_id);
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id', auth()->user()->program_keahlian_id)->pluck('id');
            $query->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['nama_lengkap', 'nip', 'tipe', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->latest();
        }

        $teachers = $query->paginate(15)->withQueryString();

        // Data for filters
        $konsentrasiQuery = \App\Models\KonsentrasiKeahlian::query();
        if (auth()->user()->konsentrasi_keahlian_id) {
            $konsentrasiQuery->where('id', auth()->user()->konsentrasi_keahlian_id);
        } elseif (auth()->user()->program_keahlian_id) {
            $konsentrasiQuery->whereIn('id', $konsentrasiIds ?? []);
        }
        $concentrations = $konsentrasiQuery->orderBy('kode')->get();

        return view('pokja.pembimbing-sekolah.index', compact('teachers', 'concentrations'));
    }

    public function create()
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $existingClasses = \App\Models\Siswa::distinct('kelas')->pluck('kelas')->filter()->values();
        $students = \App\Models\Siswa::with(['konsentrasiKeahlian', 'pembimbingSekolah', 'pembimbingSekolahUmum'])->get();
        
        // Fetch users who can be connected as a PembimbingSekolah but don't have a profile yet
        $existingUsers = \App\Models\User::whereIn('role', ['kaprog', 'pokja', 'super_admin'])
            ->whereDoesntHave('pembimbingSekolah')
            ->orderBy('name')
            ->get();

        return view('pokja.pembimbing-sekolah.create', compact('concentrations', 'existingClasses', 'students', 'existingUsers'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nip' => 'nullable|unique:pembimbing_sekolahs,nip',
            'nama_lengkap' => 'required|string|max:255',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'tipe' => 'required|in:kejuruan,umum,keduanya',
            'no_hp' => 'nullable|string',
            'mapel_cp' => 'nullable|string',
        ];

        if ($request->filled('user_id')) {
            $rules['user_id'] = 'required|exists:users,id';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['username'] = 'required|unique:users,username';
            $rules['password'] = 'required|min:6';
        }

        $request->validate($rules);

        if ($request->filled('user_id')) {
            $user = \App\Models\User::findOrFail($request->user_id);
            $user->update(['name' => $request->nama_lengkap]);
        } else {
            $user = \App\Models\User::create([
                'name' => $request->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'pembimbing_sekolah',
            ]);
        }

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

        if ($request->has('siswa_ids') && is_array($request->siswa_ids)) {
            $column = ($request->tipe === 'umum') ? 'pembimbing_sekolah_umum_id' : 'pembimbing_sekolah_id';
            \App\Models\Siswa::whereIn('id', $request->siswa_ids)
                ->update([$column => $pembimbing->id]);
        }

        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', 'Pembimbing sekolah berhasil ditambahkan.');
    }

    public function show(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $students = \App\Models\Siswa::where('pembimbing_sekolah_id', $pembimbing_sekolah->id)
            ->orWhere('pembimbing_sekolah_umum_id', $pembimbing_sekolah->id)
            ->with(['konsentrasiKeahlian', 'dudi'])
            ->get();
        return view('pokja.pembimbing-sekolah.show', compact('pembimbing_sekolah', 'students'));
    }

    public function edit(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $concentrations = \App\Models\KonsentrasiKeahlian::all();
        $existingClasses = \App\Models\Siswa::distinct('kelas')->pluck('kelas')->filter()->values();
        $currentClasses = $pembimbing_sekolah->kelasDiajar()->pluck('kelas')->toArray();
        $students = \App\Models\Siswa::with(['konsentrasiKeahlian', 'pembimbingSekolah', 'pembimbingSekolahUmum'])->get();
        return view('pokja.pembimbing-sekolah.edit', compact('pembimbing_sekolah', 'concentrations', 'existingClasses', 'currentClasses', 'students'));
    }

    public function update(Request $request, \App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $request->validate([
            'nip' => 'nullable|unique:pembimbing_sekolahs,nip,' . $pembimbing_sekolah->id,
            'nama_lengkap' => 'required|string|max:255',
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'tipe' => 'required|in:kejuruan,umum,keduanya',
            'no_hp' => 'nullable|string',
            'mapel_cp' => 'nullable|string',
        ]);

        $pembimbing_sekolah->update([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'konsentrasi_keahlian_id' => $request->konsentrasi_keahlian_id,
            'tipe' => $request->tipe,
            'no_hp' => $request->no_hp,
            'mapel_cp' => $request->mapel_cp,
        ]);
        
        if ($pembimbing_sekolah->user) {
            $pembimbing_sekolah->user->update(['name' => $request->nama_lengkap]);
        }

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

        // Sync mentored students
        $selectedSiswaIds = $request->input('siswa_ids', []);
        $oldTipe = $pembimbing_sekolah->getOriginal('tipe');
        $newTipe = $pembimbing_sekolah->tipe;
        
        $oldColumn = ($oldTipe === 'umum') ? 'pembimbing_sekolah_umum_id' : 'pembimbing_sekolah_id';
        $newColumn = ($newTipe === 'umum') ? 'pembimbing_sekolah_umum_id' : 'pembimbing_sekolah_id';

        // Detach old references
        \App\Models\Siswa::where($oldColumn, $pembimbing_sekolah->id)
            ->update([$oldColumn => null]);

        // Attach new references
        if (!empty($selectedSiswaIds)) {
            \App\Models\Siswa::whereIn('id', $selectedSiswaIds)
                ->update([$newColumn => $pembimbing_sekolah->id]);
        }

        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', 'Data pembimbing sekolah berhasil diperbarui.');
    }

    public function destroy(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $user = $pembimbing_sekolah->user;
        $pembimbing_sekolah->delete();
        if ($user && $user->role === 'pembimbing_sekolah') {
            $user->delete();
        }
        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', 'Data pembimbing sekolah berhasil dihapus.');
    }

    /**
     * Reset password individual pembimbing sekolah ke NIP masing-masing.
     */
    public function resetPassword(\App\Models\PembimbingSekolah $pembimbing_sekolah)
    {
        $user = $pembimbing_sekolah->user;
        if (!$user) {
            return back()->with('error', 'Akun pengguna pembimbing sekolah tidak ditemukan.');
        }

        $nip = trim($pembimbing_sekolah->nip ?? '');
        $cleanNip = preg_replace('/\D/', '', $nip);
        $newPassword = !empty($cleanNip) && strlen($cleanNip) >= 8 ? $cleanNip : 'smkn1ciamis';

        if (!empty($cleanNip) && strlen($cleanNip) >= 8 && $user->username !== $cleanNip) {
            $conflict = \App\Models\User::where('username', $cleanNip)->where('id', '!=', $user->id)->first();
            if ($conflict) {
                $conflict->username = $conflict->username . '_old_' . $conflict->id;
                $conflict->saveQuietly();
            }
            $user->username = $cleanNip;
            $user->email = null;
        }

        $user->password = $newPassword;
        $user->is_active = true;
        $user->force_password_change = true;
        $user->save();

        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Reset Password Pembimbing Sekolah',
                'description' => "Password guru {$pembimbing_sekolah->nama_lengkap} di-reset menjadi default NIP.",
            ]);
        }

        return back()->with('success', "Password pembimbing {$pembimbing_sekolah->nama_lengkap} berhasil di-reset menjadi NIP ({$newPassword}). Akun aktif dan wajib ganti password pada login berikutnya.");
    }

    /**
     * Reset password masal seluruh atau sebagian pembimbing sekolah ke NIP resmi masing-masing.
     */
    public function resetPasswordMassal(Request $request)
    {
        $customPassword = $request->input('custom_password');
        $teacherIds = $request->input('teacher_ids');

        $query = \App\Models\PembimbingSekolah::query()->with('user');
        if (!empty($teacherIds) && is_array($teacherIds)) {
            $query->whereIn('id', $teacherIds);
        }

        // Handle chunked / batch AJAX request (progress bar support)
        if ($request->ajax() || $request->wantsJson() || $request->isJson() || $request->has('batch_size') || $request->has('offset')) {
            $batchSize = max(1, min((int) $request->input('batch_size', 50), 100));
            $offset = max(0, (int) $request->input('offset', 0));
            $total = (clone $query)->count();

            $teachers = (clone $query)->orderBy('id')->skip($offset)->take($batchSize)->get();
            $batchProcessed = 0;

            foreach ($teachers as $teacher) {
                $user = $teacher->user;
                if (!$user) continue;

                $nip = trim($teacher->nip ?? '');
                $cleanNip = preg_replace('/\D/', '', $nip);
                $newPassword = !empty($cleanNip) && strlen($cleanNip) >= 8 ? $cleanNip : (!empty($customPassword) ? $customPassword : 'smkn1ciamis');

                if (!empty($cleanNip) && strlen($cleanNip) >= 8 && $user->username !== $cleanNip) {
                    $conflict = \App\Models\User::where('username', $cleanNip)->where('id', '!=', $user->id)->first();
                    if ($conflict) {
                        $conflict->username = $conflict->username . '_old_' . $conflict->id;
                        $conflict->saveQuietly();
                    }
                    $user->username = $cleanNip;
                    $user->email = null;
                }

                $user->password = $newPassword;
                $user->is_active = true;
                $user->force_password_change = true;
                $user->save();
                $batchProcessed++;
            }

            $nextOffset = $offset + count($teachers);
            $done = ($nextOffset >= $total) || count($teachers) === 0;

            if ($done && class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Reset Password Masal Pembimbing Sekolah',
                    'description' => "Berhasil me-reset password {$total} pembimbing sekolah ke NIP masing-masing.",
                ]);
            }

            return response()->json([
                'status' => 'success',
                'batch_processed' => $batchProcessed,
                'processed_total' => min($nextOffset, $total),
                'total' => $total,
                'offset' => $nextOffset,
                'percent' => $total > 0 ? min(100, (int) round(($nextOffset / $total) * 100)) : 100,
                'done' => $done,
                'message' => $done
                    ? "Selesai me-reset password {$total} pembimbing sekolah."
                    : "Memproses {$nextOffset} dari {$total} pembimbing...",
            ]);
        }

        // Standard Synchronous POST
        $teachers = $query->get();
        $resetCount = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($teachers, $customPassword, &$resetCount) {
            foreach ($teachers as $teacher) {
                $user = $teacher->user;
                if (!$user) continue;

                $nip = trim($teacher->nip ?? '');
                $cleanNip = preg_replace('/\D/', '', $nip);
                $newPassword = !empty($cleanNip) && strlen($cleanNip) >= 8 ? $cleanNip : (!empty($customPassword) ? $customPassword : 'smkn1ciamis');

                if (!empty($cleanNip) && strlen($cleanNip) >= 8 && $user->username !== $cleanNip) {
                    $conflict = \App\Models\User::where('username', $cleanNip)->where('id', '!=', $user->id)->first();
                    if ($conflict) {
                        $conflict->username = $conflict->username . '_old_' . $conflict->id;
                        $conflict->saveQuietly();
                    }
                    $user->username = $cleanNip;
                    $user->email = null;
                }

                $user->password = $newPassword;
                $user->is_active = true;
                $user->force_password_change = true;
                $user->save();
                $resetCount++;
            }
        });

        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Reset Password Masal Pembimbing Sekolah',
                'description' => "Berhasil me-reset password {$resetCount} guru pembimbing sekolah.",
            ]);
        }

        return redirect()->route('pokja.pembimbing_sekolah.index')
            ->with('success', "Berhasil me-reset password {$resetCount} guru pembimbing sekolah. Username dan password default telah disetel sesuai NIP masing-masing (akun aktif & wajib ganti sandi pada login awal).");
    }
}
