<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesanController extends Controller
{
    /**
     * Dapatkan daftar kontak berdasarkan role user yang login.
     */
    private function getKontak(): \Illuminate\Support\Collection
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->role;
        $kontak = collect();

        // 1. If has siswa profile
        if ($role === 'siswa') {
            $siswa = $user->siswa;
            if ($siswa) {
                if ($siswa->pembimbing_sekolah_id) {
                    $guru = \App\Models\PembimbingSekolah::with('user')
                        ->find($siswa->pembimbing_sekolah_id);
                    if ($guru && $guru->user) {
                        $kontak->push($guru->user);
                    }
                }
                if ($siswa->pembimbing_sekolah_umum_id) {
                    $guruUmum = \App\Models\PembimbingSekolah::with('user')
                        ->find($siswa->pembimbing_sekolah_umum_id);
                    if ($guruUmum && $guruUmum->user) {
                        $kontak->push($guruUmum->user);
                    }
                }
                if ($siswa->pembimbing_dudi_id) {
                    $mentor = \App\Models\PembimbingDudi::with('user')
                        ->find($siswa->pembimbing_dudi_id);
                    if ($mentor && $mentor->user) {
                        $kontak->push($mentor->user);
                    }
                }
            }

            return $kontak;
        }

        // 2. If has pembimbingSekolah profile
        if ($user->pembimbingSekolah) {
            $guru = $user->pembimbingSekolah;
            $students = \App\Models\Siswa::with('user')
                ->where('pembimbing_sekolah_id', $guru->id)
                ->orWhere('pembimbing_sekolah_umum_id', $guru->id)
                ->get()
                ->pluck('user')
                ->filter();
            $kontak = $kontak->merge($students);
        }

        // 3. If has pembimbingDudi profile
        if ($user->pembimbingDudi) {
            $mentor = $user->pembimbingDudi;
            $students = \App\Models\Siswa::with('user')
                ->where('dudi_id' . '', $mentor->dudi_id)
                ->get()
                ->pluck('user' . '')
                ->filter();
            $kontak = $kontak->merge($students);
        }

        // 4. Super admin and Pokja
        if (in_array($role, ['pokja', 'super_admin'])) {
            $others = User::where('id' . '', '!=', $user->id)
                ->where('is_active' . '', true)
                ->get();
            $kontak = $kontak->merge($others);
        }

        // Kepala Sekolah
        if ($role === 'kepala_sekolah') {
            $allowedUsers = User::whereIn('role', ['pokja', 'kaprog'])
                ->where('is_active', true)
                ->get();
            $kontak = $kontak->merge($allowedUsers);
        }

        // 5. Kaprog
        if ($role === 'kaprog') {
            // Pokja, Super Admin, & Kepala Sekolah
            $pokjas = User::whereIn('role' . '', ['pokja', 'super_admin', 'kepala_sekolah'])->where('is_active' . '', true)->where('id' . '', '!=', $user->id)->get();
            $kontak = $kontak->merge($pokjas);

            if ($user->program_keahlian_id) {
                $konsentrasiIds = \App\Models\KonsentrasiKeahlian::where('program_keahlian_id' . '', $user->program_keahlian_id)->pluck('id' . '');
                
                // Pembimbing Sekolah
                $pembimbingSekolahUsers = User::where('role' . '', 'pembimbing_sekolah')
                    ->where('is_active' . '', true)
                    ->whereHas('pembimbingSekolah', function($q) use ($konsentrasiIds) {
                        $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds);
                    })->get();
                $kontak = $kontak->merge($pembimbingSekolahUsers);

                // Pembimbing Dudi
                $pembimbingDudiUsers = User::where('role' . '', 'pembimbing_dudi')
                    ->where('is_active' . '', true)
                    ->whereHas('pembimbingDudi.dudi', function($q) use ($konsentrasiIds) {
                        $q->whereIn('konsentrasi_keahlian_id', $konsentrasiIds)
                          ->orWhereHas('konsentrasiKeahlians', function($sub) use ($konsentrasiIds) {
                              $sub->whereIn('konsentrasi_keahlians.id', $konsentrasiIds);
                          });
                    })->get();
                $kontak = $kontak->merge($pembimbingDudiUsers);
            }
        }

        // 6. Include any user with whom we already have a chat history
        $historyUserIds = Pesan::where('from_user_id' . '', $user->id)
            ->orWhere('to_user_id' . '', $user->id)
            ->get()
            ->map(function($msg) use ($user) {
                return $msg->from_user_id === $user->id ? $msg->to_user_id : $msg->from_user_id;
            })
            ->unique();

        if ($historyUserIds->isNotEmpty()) {
            $historyUsers = User::whereIn('id', $historyUserIds)->get();
            $kontak = $kontak->merge($historyUsers);
        }

        return $kontak->unique('id')->values();
    }

    /**
     * Tampilkan daftar kontak + chat terakhir.
     */
    public function index()
    {
        $kontak = $this->getKontak();
        $authId = Auth::id();

        // Hitung unread per kontak & pesan terakhir
        $kontakWithMeta = $kontak->map(function ($k) use ($authId) {
            if (!$k) return null;
            $unread = Pesan::where('from_user_id' . '', $k->id)
                ->where('to_user_id' . '', $authId)
                ->whereNull('dibaca_at')
                ->count();

            $lastMsg = Pesan::where(function ($q) use ($authId, $k) {
                    $q->where('from_user_id' . '', $authId)->where('to_user_id' . '', $k->id);
                })->orWhere(function ($q) use ($authId, $k) {
                    $q->where('from_user_id' . '', $k->id)->where('to_user_id' . '', $authId);
                })
                ->latest()
                ->first();

            return (object) [
                'user'     => $k,
                'unread'   => $unread,
                'last_msg' => $lastMsg,
            ];
        })->filter()->sortByDesc(function ($k) {
            return optional($k->last_msg)->created_at;
        })->values();

        return view('pesan.index', compact('kontakWithMeta'));
    }

    /**
     * Tampilkan thread percakapan dengan user tertentu.
     */
    public function show(User $user)
    {
        $this->authorizeKontak($user);

        $authId = Auth::id();

        // Tandai semua pesan dari user ini sebagai sudah dibaca
        Pesan::where('from_user_id' . '', $user->id)
            ->where('to_user_id' . '', $authId)
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        $messages = Pesan::where(function ($q) use ($authId, $user) {
                $q->where('from_user_id' . '', $authId)->where('to_user_id' . '', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('from_user_id' . '', $user->id)->where('to_user_id' . '', $authId);
            })
            ->orderBy('created_at' . '')
            ->get();

        $kontak = $this->getKontak();
        $authId = Auth::id();

        $kontakWithMeta = $kontak->map(function ($k) use ($authId) {
            if (!$k) return null;
            $unread = Pesan::where('from_user_id' . '', $k->id)
                ->where('to_user_id' . '', $authId)
                ->whereNull('dibaca_at')
                ->count();

            $lastMsg = Pesan::where(function ($q) use ($authId, $k) {
                    $q->where('from_user_id' . '', $authId)->where('to_user_id' . '', $k->id);
                })->orWhere(function ($q) use ($authId, $k) {
                    $q->where('from_user_id' . '', $k->id)->where('to_user_id' . '', $authId);
                })
                ->latest()
                ->first();

            return (object) [
                'user'     => $k,
                'unread'   => $unread,
                'last_msg' => $lastMsg,
            ];
        })->filter()->sortByDesc(function ($k) {
            return optional($k->last_msg)->created_at;
        })->values();

        return view('pesan.show', compact('user', 'messages', 'kontakWithMeta'));
    }

    /**
     * Kirim pesan ke satu user.
     */
    public function store(Request $request, User $user)
    {
        $this->authorizeKontak($user);

        $request->validate(['isi' => 'required|string|max:2000']);

        $pesan = Pesan::create([
            'from_user_id' => Auth::id(),
            'to_user_id'   => $user->id,
            'isi'          => $request->isi,
        ]);

        // Create notification
        Notifikasi::create([
            'from_user_id' => Auth::id(),
            'to_user_id'   => $user->id,
            'judul'        => 'Pesan Baru',
            'pesan'        => Auth::user()->name . ': ' . \Illuminate\Support\Str::limit($request->isi, 50),
            'tipe'         => 'pesan_baru',
            'is_read'      => 0
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true, 
                'id' => $pesan->id,
                'time' => $pesan->created_at->format('H:i')
            ]);
        }

        return back();
    }

    /**
     * Broadcast pesan ke semua kontak.
     */
    public function broadcast(Request $request)
    {
        $request->validate(['isi' => 'required|string|max:2000']);
        $kontak = $this->getKontak();
        $authId = Auth::id();

        foreach ($kontak as $k) {
            if (!$k) continue;
            Pesan::create([
                'from_user_id' => $authId,
                'to_user_id'   => $k->id,
                'isi'          => $request->isi,
                'is_broadcast' => true,
            ]);

            Notifikasi::create([
                'from_user_id' => $authId,
                'to_user_id'   => $k->id,
                'judul'        => 'Pesan Broadcast Baru',
                'pesan'        => Auth::user()->name . ': ' . \Illuminate\Support\Str::limit($request->isi, 50),
                'tipe'         => 'pesan_broadcast',
                'is_read'      => 0
            ]);
        }

        return back()->with('success', 'Pesan broadcast berhasil dikirim ke ' . $kontak->count() . ' kontak.');
    }

    /**
     * Ambil pesan baru (untuk polling AJAX).
     */
    public function poll(User $user, Request $request)
    {
        $this->authorizeKontak($user);

        $authId = Auth::id();
        $afterId = $request->integer('after', 0);

        $messages = Pesan::where(function ($q) use ($authId, $user) {
                $q->where('from_user_id' . '', $authId)->where('to_user_id' . '', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('from_user_id' . '', $user->id)->where('to_user_id' . '', $authId);
            })
            ->where('id' . '', '>', $afterId)
            ->orderBy('created_at' . '')
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'isi'      => $m->isi,
                'mine'     => $m->from_user_id === $authId,
                'time'     => $m->created_at->format('H:i'),
                'read'     => $m->dibaca_at !== null,
                'is_broadcast' => $m->is_broadcast,
            ]);

        // Tandai sebagai dibaca
        Pesan::where('from_user_id' . '', $user->id)
            ->where('to_user_id' . '', $authId)
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Validasi bahwa user yang diajak chat adalah kontak yang sah.
     */
    private function authorizeKontak(User $targetUser): void
    {
        $kontak = $this->getKontak();
        $allowed = $kontak->pluck('id' . '')->contains($targetUser->id);

        // Pokja / admin bisa chat dengan siapa saja
        if (in_array(Auth::user()->role, ['pokja', 'super_admin'])) return;

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki akses untuk menghubungi pengguna ini.');
        }
    }
}
