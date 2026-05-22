<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use App\Models\User;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    /**
     * Dapatkan daftar kontak berdasarkan role user yang login.
     */
    private function getKontak(): \Illuminate\Support\Collection
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role === 'siswa') {
            $siswa = $user->siswa;
            $kontak = collect();

            // Tambahkan Pembimbing Sekolah
            if ($siswa && $siswa->pembimbing_sekolah_id) {
                $guru = \App\Models\PembimbingSekolah::with('user')
                    ->find($siswa->pembimbing_sekolah_id);
                if ($guru && $guru->user) {
                    $kontak->push($guru->user);
                }
            }

            // Tambahkan Pembimbing DUDI
            if ($siswa && $siswa->pembimbing_dudi_id) {
                $mentor = \App\Models\PembimbingDudi::with('user')
                    ->find($siswa->pembimbing_dudi_id);
                if ($mentor && $mentor->user) {
                    $kontak->push($mentor->user);
                }
            }

            return $kontak;
        }

        if ($role === 'pembimbing_sekolah') {
            $guru = $user->pembimbingSekolah;
            if (!$guru) return collect();

            return \App\Models\Siswa::with('user')
                ->where('pembimbing_sekolah_id', $guru->id)
                ->get()
                ->pluck('user')
                ->filter();
        }

        if ($role === 'pembimbing_dudi') {
            $mentor = $user->pembimbingDudi;
            if (!$mentor) return collect();

            return \App\Models\Siswa::with('user')
                ->where('dudi_id', $mentor->dudi_id)
                ->get()
                ->pluck('user')
                ->filter();
        }

        if (in_array($role, ['pokja', 'super_admin'])) {
            return User::where('id', '!=', $user->id)
                ->where('is_active', true)
                ->get();
        }

        return collect();
    }

    /**
     * Tampilkan daftar kontak + chat terakhir.
     */
    public function index()
    {
        $kontak = $this->getKontak();
        $authId = auth()->id();

        // Hitung unread per kontak & pesan terakhir
        $kontakWithMeta = $kontak->map(function ($k) use ($authId) {
            if (!$k) return null;
            $unread = Pesan::where('from_user_id', $k->id)
                ->where('to_user_id', $authId)
                ->whereNull('dibaca_at')
                ->count();

            $lastMsg = Pesan::where(function ($q) use ($authId, $k) {
                    $q->where('from_user_id', $authId)->where('to_user_id', $k->id);
                })->orWhere(function ($q) use ($authId, $k) {
                    $q->where('from_user_id', $k->id)->where('to_user_id', $authId);
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

        $authId = auth()->id();

        // Tandai semua pesan dari user ini sebagai sudah dibaca
        Pesan::where('from_user_id', $user->id)
            ->where('to_user_id', $authId)
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        $messages = Pesan::where(function ($q) use ($authId, $user) {
                $q->where('from_user_id', $authId)->where('to_user_id', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('from_user_id', $user->id)->where('to_user_id', $authId);
            })
            ->orderBy('created_at')
            ->get();

        $kontak = $this->getKontak();
        $authId = auth()->id();

        $kontakWithMeta = $kontak->map(function ($k) use ($authId) {
            if (!$k) return null;
            $unread = Pesan::where('from_user_id', $k->id)
                ->where('to_user_id', $authId)
                ->whereNull('dibaca_at')
                ->count();

            $lastMsg = Pesan::where(function ($q) use ($authId, $k) {
                    $q->where('from_user_id', $authId)->where('to_user_id', $k->id);
                })->orWhere(function ($q) use ($authId, $k) {
                    $q->where('from_user_id', $k->id)->where('to_user_id', $authId);
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

        Pesan::create([
            'from_user_id' => auth()->id(),
            'to_user_id'   => $user->id,
            'isi'          => $request->isi,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
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
        $authId = auth()->id();

        foreach ($kontak as $k) {
            if (!$k) continue;
            Pesan::create([
                'from_user_id' => $authId,
                'to_user_id'   => $k->id,
                'isi'          => $request->isi,
                'is_broadcast' => true,
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

        $authId = auth()->id();
        $afterId = $request->integer('after', 0);

        $messages = Pesan::where(function ($q) use ($authId, $user) {
                $q->where('from_user_id', $authId)->where('to_user_id', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('from_user_id', $user->id)->where('to_user_id', $authId);
            })
            ->where('id', '>', $afterId)
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'isi'      => $m->isi,
                'mine'     => $m->from_user_id === $authId,
                'time'     => $m->created_at->format('H:i'),
                'read'     => $m->dibaca_at !== null,
            ]);

        // Tandai sebagai dibaca
        Pesan::where('from_user_id', $user->id)
            ->where('to_user_id', $authId)
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
        $allowed = $kontak->pluck('id')->contains($targetUser->id);

        // Pokja / admin bisa chat dengan siapa saja
        if (in_array(auth()->user()->role, ['pokja', 'super_admin'])) return;

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki akses untuk menghubungi pengguna ini.');
        }
    }
}
