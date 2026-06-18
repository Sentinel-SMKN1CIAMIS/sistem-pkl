<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
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
}
