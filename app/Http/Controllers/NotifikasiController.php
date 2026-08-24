<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('to_user_id', auth()->id())
            ->latest()
            ->paginate(20);
            
        return view('notifikasi.index', compact('notifikasis'));
    }

    public function markAsRead(Notifikasi $notifikasi, Request $request)
    {
        if ($notifikasi->to_user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->update([
            'is_read' => true,
            'read_at' => Carbon::now()
        ]);

        if ($request->has('redirect') && $notifikasi->link) {
            return redirect($notifikasi->link);
        }

        return back();
    }

    public function readAll()
    {
        Notifikasi::where('to_user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);

        return back()->with('success', 'Semua notifikasi ditandai sebagai telah dibaca.');
    }

    public function destroy(Notifikasi $notifikasi)
    {
        if ($notifikasi->to_user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function clearAll()
    {
        Notifikasi::where('to_user_id', auth()->id())->delete();

        return back()->with('success', 'Semua notifikasi berhasil dibersihkan.');
    }

    /**
     * Polling endpoint untuk notifikasi dan pesan baru ke perangkat secara real-time.
     */
    public function poll(Request $request)
    {
        $userId = auth()->id();
        $lastNotifId = $request->integer('last_notif_id', 0);
        $lastPesanId = $request->integer('last_pesan_id', 0);

        // Ambil notifikasi sistem baru (kecuali pesan chat, karena pesan chat ditangani khusus oleh blok messages)
        $newNotifs = Notifikasi::where('to_user_id', $userId)
            ->where('is_read', false)
            ->whereNotIn('tipe', ['pesan_baru', 'pesan_broadcast'])
            ->when($lastNotifId > 0, function ($q) use ($lastNotifId) {
                $q->where('id', '>', $lastNotifId);
            })
            ->latest()
            ->take(5)
            ->get();

        // Ambil pesan chat baru sejak ID terakhir yang diterima client
        $newPesans = \App\Models\Pesan::with('fromUser')
            ->where('to_user_id', $userId)
            ->whereNull('dibaca_at')
            ->when($lastPesanId > 0, function ($q) use ($lastPesanId) {
                $q->where('id', '>', $lastPesanId);
            })
            ->latest()
            ->take(5)
            ->get();

        // Unread notifikasi sistem (tanpa pesan chat yang sudah punya badge sendiri di menu Pesan)
        $totalUnreadNotif = Notifikasi::where('to_user_id', $userId)
            ->where('is_read', false)
            ->whereNotIn('tipe', ['pesan_baru', 'pesan_broadcast'])
            ->count();
        $totalUnreadPesan = \App\Models\Pesan::where('to_user_id', $userId)->whereNull('dibaca_at')->count();

        $maxNotifId = Notifikasi::where('to_user_id', $userId)->max('id') ?? 0;
        $maxPesanId = \App\Models\Pesan::where('to_user_id', $userId)->max('id') ?? 0;

        return response()->json([
            'notifications' => $newNotifs->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->judul ?? 'Notifikasi MAS-PKL',
                'body' => $n->pesan,
                'url' => $n->link ?? route('notifications.index'),
                'type' => $n->tipe ?? 'umum',
                'created_at' => $n->created_at?->diffForHumans() ?? 'Baru saja',
            ]),
            'messages' => $newPesans->map(fn($p) => [
                'id' => $p->id,
                'title' => 'Pesan dari ' . ($p->fromUser?->name ?? 'Pengguna'),
                'body' => \Illuminate\Support\Str::limit($p->isi, 80),
                'url' => route('pesan.show', $p->from_user_id),
                'sender' => $p->fromUser?->name ?? 'Pengguna',
                'created_at' => $p->created_at?->diffForHumans() ?? 'Baru saja',
            ]),
            'unread_notif_count' => $totalUnreadNotif,
            'unread_pesan_count' => $totalUnreadPesan,
            'max_notif_id' => $maxNotifId,
            'max_pesan_id' => $maxPesanId,
        ]);
    }
}
