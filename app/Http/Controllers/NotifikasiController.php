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

    public function markAsRead(Notifikasi $notifikasi)
    {
        if ($notifikasi->to_user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->update([
            'is_read' => true,
            'read_at' => Carbon::now()
        ]);

        return back();
    }
}
